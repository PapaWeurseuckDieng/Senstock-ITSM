@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')
@section('page-subtitle', 'Vue d\'ensemble — ' . now()->format('d F Y'))

@section('content')

{{-- Stats principales --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon brand"><i class="fa-solid fa-ticket"></i></div>
        <div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total tickets</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info"><i class="fa-solid fa-circle-dot"></i></div>
        <div>
            <div class="stat-value">{{ $stats['ouverts'] }}</div>
            <div class="stat-label">Tickets ouverts</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-spinner"></i></div>
        <div>
            <div class="stat-value">{{ $stats['en_cours'] }}</div>
            <div class="stat-label">En cours</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="fa-solid fa-circle-check"></i></div>
        <div>
            <div class="stat-value">{{ $stats['resolus'] }}</div>
            <div class="stat-label">Résolus</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
            <div class="stat-value">{{ $stats['overdue'] }}</div>
            <div class="stat-label">En retard SLA</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="fa-solid fa-gauge"></i></div>
        <div>
            <div class="stat-value">{{ $stats['sla_rate'] }}%</div>
            <div class="stat-label">Taux SLA respecté</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info"><i class="fa-solid fa-stopwatch"></i></div>
        <div>
            <div class="stat-value">{{ $stats['avg_resolution_hours'] }}h</div>
            <div class="stat-label">Délai moyen résolution</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-star"></i></div>
        <div>
            <div class="stat-value">{{ $stats['avg_satisfaction'] }}/5</div>
            <div class="stat-label">Satisfaction moyenne</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">

    {{-- Répartition par priorité --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-chart-pie" style="color:var(--brand);margin-right:8px;"></i>Tickets par priorité</span>
        </div>
        <div class="card-body">
            @foreach(['critique' => ['Critique', 'danger'], 'haute' => ['Haute', 'warning'], 'normale' => ['Normale', 'info'], 'faible' => ['Faible', 'secondary']] as $key => [$label, $color])
            @php $count = $byPriority[$key] ?? 0; $total = $byPriority->sum() ?: 1; $pct = round($count / $total * 100); @endphp
            <div style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span class="badge badge-{{ $key }}">{{ $label }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--gray-700);">{{ $count }} <span style="color:var(--gray-400);font-weight:400;">({{ $pct }}%)</span></span>
                </div>
                <div style="height:6px;background:var(--gray-100);border-radius:3px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:var(--brand);border-radius:3px;transition:width .6s ease;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Répartition par type --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-tags" style="color:var(--brand);margin-right:8px;"></i>Tickets par type</span>
        </div>
        <div class="card-body">
            @foreach(['incident' => 'Incident', 'panne' => 'Panne', 'demande' => 'Demande', 'changement' => 'Changement'] as $key => $label)
            @php $count = $byType[$key] ?? 0; $total = $byType->sum() ?: 1; $pct = round($count / $total * 100); @endphp
            <div style="margin-bottom:14px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                    <span class="badge badge-{{ $key }}">{{ $label }}</span>
                    <span style="font-size:13px;font-weight:600;color:var(--gray-700);">{{ $count }} <span style="color:var(--gray-400);font-weight:400;">({{ $pct }}%)</span></span>
                </div>
                <div style="height:6px;background:var(--gray-100);border-radius:3px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:var(--brand-dark);border-radius:3px;transition:width .6s ease;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Tickets récents --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--brand);margin-right:8px;"></i>Tickets récents</span>
        <a href="{{ route('tickets.index') }}" class="topbar-btn btn-outline btn-sm">Voir tout</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N° Ticket</th>
                    <th>Titre</th>
                    <th>Demandeur</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Assigné à</th>
                    <th>SLA</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTickets as $ticket)
                <tr>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}" class="ticket-link">
                            <span class="ticket-number">{{ $ticket->ticket_number }}</span>
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}" class="ticket-link" style="font-weight:500;color:var(--gray-800);">
                            {{ Str::limit($ticket->title, 45) }}
                        </a>
                    </td>
                    <td style="color:var(--gray-500);font-size:12px;">{{ $ticket->user->name }}</td>
                    <td><span class="badge badge-{{ $ticket->priority }}">{{ $ticket->priority_label }}</span></td>
                    <td><span class="badge badge-{{ $ticket->status }}">{{ $ticket->status_label }}</span></td>
                    <td style="font-size:12px;color:var(--gray-500);">{{ $ticket->assignee?->name ?? '—' }}</td>
                    <td>
                        @if($ticket->sla_breached)
                            <span class="sla-breach"><i class="fa-solid fa-circle-exclamation"></i> Dépassé</span>
                        @elseif($ticket->is_overdue)
                            <span class="sla-warning"><i class="fa-solid fa-triangle-exclamation"></i> En retard</span>
                        @else
                            <span class="sla-ok"><i class="fa-solid fa-check"></i> OK</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--gray-400);">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:var(--gray-400);padding:32px;">Aucun ticket</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Performance techniciens --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-users-gear" style="color:var(--brand);margin-right:8px;"></i>Performance des techniciens</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Technicien</th>
                    <th>Tickets assignés</th>
                    <th>Résolus</th>
                    <th>Taux résolution</th>
                </tr>
            </thead>
            <tbody>
                @forelse($technicianPerf as $tech)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--brand-pale);color:var(--brand-dark);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;">
                                {{ strtoupper(substr($tech->name, 0, 2)) }}
                            </div>
                            <span style="font-weight:500;">{{ $tech->name }}</span>
                        </div>
                    </td>
                    <td>{{ $tech->total_assigned }}</td>
                    <td>{{ $tech->resolved_count }}</td>
                    <td>
                        @php $rate = $tech->total_assigned > 0 ? round($tech->resolved_count / $tech->total_assigned * 100) : 0; @endphp
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:6px;background:var(--gray-100);border-radius:3px;overflow:hidden;">
                                <div style="height:100%;width:{{ $rate }}%;background:var(--success);border-radius:3px;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:600;color:var(--gray-600);">{{ $rate }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--gray-400);padding:24px;">Aucun technicien</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
