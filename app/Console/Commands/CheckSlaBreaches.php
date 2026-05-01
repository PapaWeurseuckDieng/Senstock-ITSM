<?php

namespace App\Console\Commands;

use App\Mail\SlaBreachMail;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckSlaBreaches extends Command
{
    protected $signature   = 'itsm:check-sla';
    protected $description = 'Vérifie les tickets dont le SLA est dépassé et envoie des alertes.';

    public function handle(): int
    {
        $overdueTickets = Ticket::overdue()
            ->where('sla_breached', false)
            ->with(['user', 'assignee'])
            ->get();

        if ($overdueTickets->isEmpty()) {
            $this->info('Aucun ticket en dépassement SLA.');
            return Command::SUCCESS;
        }

        foreach ($overdueTickets as $ticket) {
            // Marquer comme SLA dépassé
            $ticket->update(['sla_breached' => true]);

            // Audit
            AuditLog::record('ticket.sla_breached', $ticket, [], ['sla_breached' => true],
                "SLA dépassé pour {$ticket->ticket_number}");

            // Notifier le technicien assigné
            if ($ticket->assignee) {
                Mail::to($ticket->assignee->email)->queue(new SlaBreachMail($ticket));
            }

            // Notifier le responsable IT
            $managers = User::whereIn('role', ['responsable_it', 'administrateur'])
                ->where('is_active', true)
                ->get();
            foreach ($managers as $manager) {
                Mail::to($manager->email)->queue(new SlaBreachMail($ticket));
            }

            $this->warn("SLA dépassé: {$ticket->ticket_number} - {$ticket->title}");
        }

        $this->info("Alertes SLA envoyées pour {$overdueTickets->count()} ticket(s).");
        return Command::SUCCESS;
    }
}
