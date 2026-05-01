<?php

namespace App\Services;

use App\Mail\TicketAssignedMail;
use App\Mail\TicketCreatedMail;
use App\Mail\TicketResolvedMail;
use App\Mail\TicketStatusChangedMail;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TicketService
{
    /**
     * Crée un nouveau ticket et envoie les notifications.
     */
    public function create(array $data, User $user): Ticket
    {
        $ticket = Ticket::create(array_merge($data, ['user_id' => $user->id]));

        // Pièces jointes
        if (!empty($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $this->storeAttachment($ticket, $file, $user);
            }
        }

        // Audit
        AuditLog::record('ticket.created', $ticket, [], $ticket->toArray(),
            "Ticket {$ticket->ticket_number} créé par {$user->name}");

        // Notifications email
        $this->notifyITOnCreation($ticket);

        return $ticket;
    }

    /**
     * Assigne un ticket à un technicien.
     */
    public function assign(Ticket $ticket, User $technician, User $assignedBy): Ticket
    {
        $old = ['assigned_to' => $ticket->assigned_to, 'status' => $ticket->status];

        $ticket->update([
            'assigned_to'   => $technician->id,
            'status'        => 'en_cours',
            't1_assigned_at'=> now(),
            't2_started_at' => now(),
        ]);

        AuditLog::record('ticket.assigned', $ticket, $old, $ticket->fresh()->toArray(),
            "Ticket assigné à {$technician->name} par {$assignedBy->name}");

        // Notifier le technicien
        Mail::to($technician->email)->queue(new TicketAssignedMail($ticket->fresh()));

        // Notifier le demandeur
        Mail::to($ticket->user->email)->queue(new TicketStatusChangedMail($ticket->fresh(), $old['status'], 'en_cours'));

        return $ticket->fresh();
    }

    /**
     * Met à jour le statut d'un ticket.
     */
    public function updateStatus(Ticket $ticket, string $newStatus, User $updatedBy): Ticket
    {
        $oldStatus = $ticket->status;

        $timestamps = match($newStatus) {
            'en_attente' => ['t3_pending_at' => now()],
            'resolu'     => ['t4_resolved_at' => now()],
            'ferme'      => ['t6_closed_at' => now()],
            default      => [],
        };

        // Vérifier SLA si résolution
        if ($newStatus === 'resolu' && $ticket->sla_deadline?->isPast()) {
            $timestamps['sla_breached'] = true;
        }

        $ticket->update(array_merge(['status' => $newStatus], $timestamps));

        AuditLog::record("ticket.status.{$newStatus}", $ticket,
            ['status' => $oldStatus], ['status' => $newStatus],
            "Statut changé de '{$oldStatus}' à '{$newStatus}' par {$updatedBy->name}");

        // Notifications
        $this->sendStatusNotifications($ticket->fresh(), $oldStatus, $newStatus);

        return $ticket->fresh();
    }

    /**
     * Résoudre un ticket avec note de résolution.
     */
    public function resolve(Ticket $ticket, string $resolutionNote, User $technician): Ticket
    {
        $old = $ticket->toArray();

        $slaBreached = $ticket->sla_deadline?->isPast() ?? false;

        $ticket->update([
            'status'          => 'resolu',
            'resolution_note' => $resolutionNote,
            't4_resolved_at'  => now(),
            'sla_breached'    => $slaBreached,
        ]);

        AuditLog::record('ticket.resolved', $ticket, $old, $ticket->fresh()->toArray(),
            "Ticket résolu par {$technician->name}");

        // Email au demandeur
        Mail::to($ticket->user->email)->queue(new TicketResolvedMail($ticket->fresh()));

        // Email responsable IT + admin
        $this->notifyITManagerOnResolve($ticket->fresh());

        return $ticket->fresh();
    }

    /**
     * Validation par l'utilisateur.
     */
    public function validateResolution(Ticket $ticket, int $satisfactionScore, ?string $comment): Ticket
    {
        $ticket->update([
            'user_validated'       => true,
            't5_validated_at'      => now(),
            'satisfaction_score'   => $satisfactionScore,
            'satisfaction_comment' => $comment,
            'status'               => 'ferme',
            't6_closed_at'         => now(),
        ]);

        AuditLog::record('ticket.validated', $ticket, [], $ticket->fresh()->toArray(),
            "Résolution validée par l'utilisateur. Score: {$satisfactionScore}/5");

        return $ticket->fresh();
    }

    // ─── Notifications privées ───────────────────────────────────────────────

    private function notifyITOnCreation(Ticket $ticket): void
    {
        // Service technique
        $itTeamEmail = config('mail.it_team_email', env('IT_TEAM_EMAIL'));
        if ($itTeamEmail) {
            Mail::to($itTeamEmail)->queue(new TicketCreatedMail($ticket));
        }

        // Techniciens actifs
        $technicians = User::where('role', 'technicien')
            ->where('is_active', true)
            ->get();

        foreach ($technicians as $tech) {
            Mail::to($tech->email)->queue(new TicketCreatedMail($ticket));
        }

        // Pour les pannes : notifier aussi le responsable IT
        if ($ticket->type === 'panne' || $ticket->priority === 'critique') {
            $managers = User::where('role', 'responsable_it')
                ->where('is_active', true)
                ->get();
            foreach ($managers as $manager) {
                Mail::to($manager->email)->queue(new TicketCreatedMail($ticket));
            }
        }
    }

    private function sendStatusNotifications(Ticket $ticket, string $oldStatus, string $newStatus): void
    {
        // Notifier le demandeur
        Mail::to($ticket->user->email)
            ->queue(new TicketStatusChangedMail($ticket, $oldStatus, $newStatus));

        // Notifier le technicien assigné si différent du demandeur
        if ($ticket->assigned_to && $ticket->assignee->email !== $ticket->user->email) {
            Mail::to($ticket->assignee->email)
                ->queue(new TicketStatusChangedMail($ticket, $oldStatus, $newStatus));
        }
    }

    private function notifyITManagerOnResolve(Ticket $ticket): void
    {
        $itManagerEmail = config('mail.it_manager_email', env('IT_MANAGER_EMAIL'));
        if ($itManagerEmail) {
            Mail::to($itManagerEmail)->queue(new TicketResolvedMail($ticket));
        }
    }

    private function storeAttachment(Ticket $ticket, UploadedFile $file, User $user): void
    {
        $path = $file->store("tickets/{$ticket->id}/attachments", 'private');

        $ticket->attachments()->create([
            'user_id'           => $user->id,
            'filename'          => basename($path),
            'original_filename' => $file->getClientOriginalName(),
            'mime_type'         => $file->getMimeType(),
            'file_size'         => $file->getSize(),
            'path'              => $path,
        ]);
    }
}
