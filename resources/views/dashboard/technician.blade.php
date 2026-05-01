@extends('layouts.app')

@section('title', 'Dashboard Technicien')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Mon Tableau de Bord</h1>
        <p class="page-subtitle">Tickets assignés et en attente — {{ now()->format('d/m/Y') }}</p>
    </div>
</div>

{{-- KPI Cards --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">

    <div class="stat-card" style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #889ABF;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:.75rem; font-weight:600; color:#889ABF; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .5rem;">Mes tickets ouverts</p>
                <p style="font-size:2rem; font-weight:700; color:#1A1F2E; margin:0;">{{ $myOpenTickets }}</p>
            </div>
            <div style="width:44px; height:44px; background:rgba(136,154,191,.12); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="#889ABF" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #F59E0B;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:.75rem; font-weight:600; color:#F59E0B; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .5rem;">En cours</p>
                <p style="font-size:2rem; font-weight:700; color:#1A1F2E; margin:0;">{{ $myInProgressTickets }}</p>
            </div>
            <div style="width:44px; height:44px; background:rgba(245,158,11,.12); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="#F59E0B" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #EF4444;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:.75rem; font-weight:600; color:#EF4444; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .5rem;">SLA dépassés</p>
                <p style="font-size:2rem; font-weight:700; color:#1A1F2E; margin:0;">{{ $myOverdueTickets }}</p>
            </div>
            <div style="width:44px; height:44px; background:rgba(239,68,68,.12); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="#EF4444" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #10B981;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:.75rem; font-weight:600; color:#10B981; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .5rem;">Résolus ce mois</p>
                <p style="font-size:2rem; font-weight:700; color:#1A1F2E; margin:0;">{{ $myResolvedThisMonth }}</p>
            </div>
            <div style="width:44px; height:44px; background:rgba(16,185,129,.12); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="#10B981" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card" style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #8B5CF6;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
            <div>
                <p style="font-size:.75rem; font-weight:600; color:#8B5CF6; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .5rem;">Non assignés</p>
                <p style="font-size:2rem; font-weight:700; color:#1A1F2E; margin:0;">{{ $unassignedTickets }}</p>
            </div>
            <div style="width:44px; height:44px; background:rgba(139,92,246,.12); border-radius:10px; display:flex; align-items:center; justify-content:center;">
                <svg width="20" height="20" fill="none" stroke="#8B5CF6" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem;">

    {{-- Tickets urgents / priorité critique --}}
    <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h3 style="font-size:.9rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem; display:flex; align-items:center; gap:.5rem;">
            <svg width="16" height="16" fill="none" stroke="#EF4444" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            Tickets critiques / haute priorité
        </h3>
        @forelse($urgentTickets as $ticket)
        <div style="padding:.75rem; border-radius:8px; background:#FEF2F2; border:1px solid #FCA5A5; margin-bottom:.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <p style="font-size:.8rem; font-weight:700; color:#1A1F2E; margin:0;">{{ $ticket->ticket_number }}</p>
                    <p style="font-size:.75rem; color:#6B7280; margin:.2rem 0 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;">{{ $ticket->title }}</p>
                </div>
                <div style="text-align:right;">
                    @if($ticket->sla_breached)
                        <span style="font-size:.7rem; font-weight:600; color:#fff; background:#EF4444; padding:.2rem .5rem; border-radius:20px;">SLA !</span>
                    @else
                        <span style="font-size:.7rem; color:#6B7280;">
                            @if($ticket->sla_deadline)
                                Échéance: {{ $ticket->sla_deadline->format('d/m H:i') }}
                            @endif
                        </span>
                    @endif
                </div>
            </div>
            <div style="margin-top:.5rem;">
                <a href="{{ route('tickets.show', $ticket) }}" style="font-size:.75rem; color:#889ABF; font-weight:600; text-decoration:none;">Traiter →</a>
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:2rem; color:#9CA3AF;">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.5rem; display:block;"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <p style="margin:0; font-size:.8rem;">Aucun ticket critique en attente</p>
        </div>
        @endforelse
    </div>

    {{-- Tickets non assignés --}}
    <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h3 style="font-size:.9rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem; display:flex; align-items:center; gap:.5rem;">
            <svg width="16" height="16" fill="none" stroke="#8B5CF6" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Tickets à prendre en charge
        </h3>
        @forelse($pendingUnassigned as $ticket)
        <div style="padding:.75rem; border-radius:8px; background:#F5F3FF; border:1px solid #C4B5FD; margin-bottom:.5rem;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <p style="font-size:.8rem; font-weight:700; color:#1A1F2E; margin:0;">{{ $ticket->ticket_number }}</p>
                    <p style="font-size:.75rem; color:#6B7280; margin:.2rem 0 0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">{{ $ticket->title }}</p>
                </div>
                <span class="badge-priority" data-priority="{{ $ticket->priority }}"
                    style="font-size:.7rem; font-weight:600; padding:.2rem .6rem; border-radius:20px;
                    background:{{ $ticket->priority === 'critique' ? '#FEE2E2' : ($ticket->priority === 'haute' ? '#FEF3C7' : '#F0FDF4') }};
                    color:{{ $ticket->priority === 'critique' ? '#DC2626' : ($ticket->priority === 'haute' ? '#D97706' : '#16A34A') }};">
                    {{ $ticket->priority_label }}
                </span>
            </div>
            <div style="margin-top:.5rem; display:flex; gap:.5rem;">
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="assigned_to" value="{{ auth()->id() }}">
                    <button type="submit" style="font-size:.75rem; color:#fff; background:#889ABF; border:none; padding:.25rem .75rem; border-radius:6px; cursor:pointer; font-weight:600;">
                        M'assigner
                    </button>
                </form>
                <a href="{{ route('tickets.show', $ticket) }}" style="font-size:.75rem; color:#889ABF; font-weight:600; text-decoration:none; padding:.25rem .5rem;">Voir →</a>
            </div>
        </div>
        @empty
        <div style="text-align:center; padding:2rem; color:#9CA3AF;">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.5rem; display:block;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <p style="margin:0; font-size:.8rem;">Tous les tickets sont assignés</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Mes tickets en cours --}}
<div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h3 style="font-size:.9rem; font-weight:700; color:#1A1F2E; margin:0;">Mes tickets en cours de traitement</h3>
        <a href="{{ route('tickets.index') }}" style="font-size:.8rem; color:#889ABF; font-weight:600; text-decoration:none;">Voir tout →</a>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:.82rem;">
            <thead>
                <tr style="border-bottom:2px solid #F3F4F6;">
                    <th style="text-align:left; padding:.75rem .5rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Référence</th>
                    <th style="text-align:left; padding:.75rem .5rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Titre</th>
                    <th style="text-align:left; padding:.75rem .5rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Demandeur</th>
                    <th style="text-align:left; padding:.75rem .5rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Priorité</th>
                    <th style="text-align:left; padding:.75rem .5rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">SLA</th>
                    <th style="text-align:left; padding:.75rem .5rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Statut</th>
                    <th style="text-align:left; padding:.75rem .5rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase; letter-spacing:.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($myActiveTickets as $ticket)
                <tr style="border-bottom:1px solid #F9FAFB; transition:background .15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background=''">
                    <td style="padding:.75rem .5rem;">
                        <span style="font-family:'DM Mono', monospace; font-size:.78rem; color:#889ABF; font-weight:600;">{{ $ticket->ticket_number }}</span>
                    </td>
                    <td style="padding:.75rem .5rem; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-weight:500; color:#1A1F2E;">
                        {{ $ticket->title }}
                    </td>
                    <td style="padding:.75rem .5rem; color:#6B7280;">{{ $ticket->user?->name ?? "—" }}</td>
                    <td style="padding:.75rem .5rem;">
                        <span style="font-size:.72rem; font-weight:600; padding:.25rem .6rem; border-radius:20px;
                            background:{{ $ticket->priority === 'critique' ? '#FEE2E2' : ($ticket->priority === 'haute' ? '#FEF3C7' : ($ticket->priority === 'normale' ? '#DBEAFE' : '#F0FDF4')) }};
                            color:{{ $ticket->priority === 'critique' ? '#DC2626' : ($ticket->priority === 'haute' ? '#D97706' : ($ticket->priority === 'normale' ? '#2563EB' : '#16A34A')) }};">
                            {{ $ticket->priority_label }}
                        </span>
                    </td>
                    <td style="padding:.75rem .5rem;">
                        @if($ticket->sla_breached)
                            <span style="font-size:.72rem; font-weight:600; color:#DC2626; display:flex; align-items:center; gap:.25rem;">
                                <svg width="12" height="12" fill="#DC2626" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>
                                Dépassé
                            </span>
                        @elseif($ticket->sla_deadline)
                            @php $hoursLeft = now()->diffInHours($ticket->sla_deadline, false); @endphp
                            <span style="font-size:.72rem; font-weight:600; color:{{ $hoursLeft < 2 ? '#EF4444' : ($hoursLeft < 8 ? '#F59E0B' : '#10B981') }};">
                                @if($hoursLeft < 0)
                                    Dépassé
                                @elseif($hoursLeft < 1)
                                    &lt; 1h
                                @else
                                    {{ $hoursLeft }}h restantes
                                @endif
                            </span>
                        @else
                            <span style="color:#9CA3AF; font-size:.72rem;">—</span>
                        @endif
                    </td>
                    <td style="padding:.75rem .5rem;">
                        <span style="font-size:.72rem; font-weight:600; padding:.25rem .6rem; border-radius:20px;
                            background:{{ $ticket->status === 'en_cours' ? '#DBEAFE' : '#FEF3C7' }};
                            color:{{ $ticket->status === 'en_cours' ? '#2563EB' : '#D97706' }};">
                            {{ $ticket->status_label }}
                        </span>
                    </td>
                    <td style="padding:.75rem .5rem;">
                        <a href="{{ route('tickets.show', $ticket) }}" style="font-size:.78rem; color:#889ABF; font-weight:600; text-decoration:none; background:rgba(136,154,191,.1); padding:.3rem .75rem; border-radius:6px;">
                            Traiter
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding:3rem; color:#9CA3AF;">
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 0.75rem; display:block;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                        <p style="margin:0; font-size:.85rem;">Aucun ticket en cours</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
