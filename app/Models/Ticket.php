<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number', 'user_id', 'assigned_to', 'title', 'description',
        'type', 'priority', 'status', 'category',
        't0_created_at', 't1_assigned_at', 't2_started_at',
        't3_pending_at', 't4_resolved_at', 't5_validated_at', 't6_closed_at',
        'sla_duration_hours', 'sla_deadline', 'sla_breached',
        'resolution_note', 'user_validated', 'satisfaction_score', 'satisfaction_comment',
    ];

    protected $casts = [
        't0_created_at'    => 'datetime',
        't1_assigned_at'   => 'datetime',
        't2_started_at'    => 'datetime',
        't3_pending_at'    => 'datetime',
        't4_resolved_at'   => 'datetime',
        't5_validated_at'  => 'datetime',
        't6_closed_at'     => 'datetime',
        'sla_deadline'     => 'datetime',
        'sla_breached'     => 'boolean',
        'user_validated'   => 'boolean',
    ];

    // ─── SLA par priorité (en heures) ─────────────────────────────────────

    const SLA_HOURS = [
        'critique' => 4,
        'haute'    => 8,
        'normale'  => 24,
        'faible'   => 72,
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    // ─── Boot ────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
            $ticket->t0_created_at = now();
            $ticket->sla_duration_hours = self::SLA_HOURS[$ticket->priority] ?? 24;
            $ticket->sla_deadline = now()->addHours($ticket->sla_duration_hours);
        });
    }

    // ─── Génération du numéro unique ─────────────────────────────────────────

    public static function generateTicketNumber(): string
    {
        $year = now()->format('Y');
        $last = self::whereYear('created_at', $year)->lockForUpdate()->max('id') ?? 0;
        return sprintf('TCK-%s-%05d', $year, $last + 1);
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'critique' => 'danger',
            'haute'    => 'warning',
            'normale'  => 'info',
            'faible'   => 'secondary',
            default    => 'secondary',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'ouvert'      => 'primary',
            'en_cours'    => 'warning',
            'en_attente'  => 'secondary',
            'resolu'      => 'success',
            'ferme'       => 'dark',
            'annule'      => 'danger',
            default       => 'secondary',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'critique' => 'Critique',
            'haute'    => 'Haute',
            'normale'  => 'Normale',
            'faible'   => 'Faible',
            default    => 'Normale',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'ouvert'      => 'Ouvert',
            'en_cours'    => 'En cours',
            'en_attente'  => 'En attente',
            'resolu'      => 'Résolu',
            'ferme'       => 'Fermé',
            'annule'      => 'Annulé',
            default       => 'Ouvert',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'incident'   => 'Incident',
            'demande'    => 'Demande',
            'panne'      => 'Panne',
            'changement' => 'Changement',
            default      => 'Incident',
        };
    }

    // Durée de traitement (T0 → T4) en minutes
    public function getResolutionTimeAttribute(): ?int
    {
        if ($this->t0_created_at && $this->t4_resolved_at) {
            return $this->t0_created_at->diffInMinutes($this->t4_resolved_at);
        }
        return null;
    }

    // SLA respecté ?
    public function getIsSlaOkAttribute(): bool
    {
        return !$this->sla_breached;
    }

    // Ticket en retard ?
    public function getIsOverdueAttribute(): bool
    {
        return $this->sla_deadline
            && !in_array($this->status, ['resolu', 'ferme', 'annule'])
            && now()->isAfter($this->sla_deadline);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'ouvert');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'en_cours');
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['resolu', 'ferme', 'annule'])
                     ->where('sla_deadline', '<', now());
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAssignedTo($query, int $technicianId)
    {
        return $query->where('assigned_to', $technicianId);
    }
}
