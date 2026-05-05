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

{{-- Suivi Parc Informatique --}}
<div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
        <h3 style="font-size:.9rem; font-weight:700; color:#1A1F2E; margin:0; display:flex; align-items:center; gap:.5rem;">
            <svg width="16" height="16" fill="none" stroke="#889ABF" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Parc Informatique
        </h3>
        <a href="{{ route('parc.index') }}" style="font-size:.8rem; color:#889ABF; font-weight:600; text-decoration:none;">Gérer le parc →</a>
    </div>
    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:1rem;">
        <div style="text-align:center; padding:1rem; background:#F9FAFB; border-radius:10px; border-top:3px solid #889ABF;">
            <p style="font-size:1.6rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $parcStats['total'] }}</p>
            <p style="font-size:.72rem; color:#9CA3AF; margin:.3rem 0 0; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Total</p>
        </div>
        <div style="text-align:center; padding:1rem; background:#F0FDF4; border-radius:10px; border-top:3px solid #10B981;">
            <p style="font-size:1.6rem; font-weight:800; color:#065F46; margin:0;">{{ $parcStats['actifs'] }}</p>
            <p style="font-size:.72rem; color:#10B981; margin:.3rem 0 0; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Actifs</p>
        </div>
        <div style="text-align:center; padding:1rem; background:#FFFBEB; border-radius:10px; border-top:3px solid #F59E0B;">
            <p style="font-size:1.6rem; font-weight:800; color:#92400E; margin:0;">{{ $parcStats['en_maintenance'] }}</p>
            <p style="font-size:.72rem; color:#F59E0B; margin:.3rem 0 0; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Maintenance</p>
        </div>
        <div style="text-align:center; padding:1rem; background:#FEF2F2; border-radius:10px; border-top:3px solid #EF4444;">
            <p style="font-size:1.6rem; font-weight:800; color:#991B1B; margin:0;">{{ $parcStats['hors_service'] }}</p>
            <p style="font-size:.72rem; color:#EF4444; margin:.3rem 0 0; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Hors service</p>
        </div>
        <div style="text-align:center; padding:1rem; background:#FEF2F2; border-radius:10px; border-top:3px solid #DC2626;">
            <p style="font-size:1.6rem; font-weight:800; color:#991B1B; margin:0;">{{ $parcStats['garantie_expire'] }}</p>
            <p style="font-size:.72rem; color:#DC2626; margin:.3rem 0 0; font-weight:600; text-transform:uppercase; letter-spacing:.04em;">Garantie expirée</p>
        </div>
    </div>
    @if($parcParCategorie->count())
    <div style="margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid #F3F4F6; display:flex; gap:.75rem; flex-wrap:wrap;">
        @php
        $catColors = ['ordinateur_bureau'=>'#889ABF','ordinateur_portable'=>'#8B5CF6','serveur'=>'#EF4444','imprimante'=>'#F59E0B','switch'=>'#10B981','routeur'=>'#06B6D4','onduleur'=>'#F97316','iot'=>'#EC4899','telephone_ip'=>'#14B8A6','autre'=>'#9CA3AF'];
        $catLabels = \App\Models\Equipement::CATEGORIES;
        @endphp
        @foreach($parcParCategorie as $cat => $total)
        <span style="display:inline-flex; align-items:center; gap:.35rem; background:#F9FAFB; border:1px solid #E5E7EB; padding:.3rem .7rem; border-radius:20px; font-size:.75rem; font-weight:600; color:#374151;">
            <span style="width:7px; height:7px; border-radius:50%; background:{{ $catColors[$cat] ?? '#9CA3AF' }}; display:inline-block; flex-shrink:0;"></span>
            {{ $catLabels[$cat] ?? $cat }} — {{ $total }}
        </span>
        @endforeach
    </div>
    @endif
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
