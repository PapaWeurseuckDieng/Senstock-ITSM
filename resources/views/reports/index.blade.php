@extends('layouts.app')

@section('title', 'Rapports et Exports')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Rapports & Exports</h1>
        <p class="page-subtitle">Analyse des performances IT — {{ now()->format('F Y') }}</p>
    </div>
</div>

{{-- KPI synthèse --}}
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:1.25rem; margin-bottom:2rem;">

    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#889ABF; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">Total tickets</p>
        <p style="font-size:2.2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $stats['total'] }}</p>
    </div>

    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#10B981; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">Résolus</p>
        <p style="font-size:2.2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $stats['resolved'] }}</p>
        <p style="font-size:.75rem; color:#10B981; margin:.2rem 0 0;">{{ $stats['total'] > 0 ? round($stats['resolved'] / $stats['total'] * 100) : 0 }}%</p>
    </div>

    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#F59E0B; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">En cours</p>
        <p style="font-size:2.2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $stats['in_progress'] }}</p>
    </div>

    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:{{ $stats['sla_rate'] >= 80 ? '#10B981' : '#EF4444' }}; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">Taux SLA</p>
        <p style="font-size:2.2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $stats['sla_rate'] }}%</p>
        <p style="font-size:.72rem; color:{{ $stats['sla_rate'] >= 80 ? '#10B981' : '#EF4444' }}; margin:.2rem 0 0;">
            {{ $stats['sla_rate'] >= 80 ? '✓ Objectif atteint' : '⚠ Objectif non atteint' }}
        </p>
    </div>

    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#8B5CF6; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">Délai moyen</p>
        <p style="font-size:2.2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $stats['avg_resolution_hours'] }}h</p>
        <p style="font-size:.72rem; color:#9CA3AF; margin:.2rem 0 0;">de résolution</p>
    </div>

    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#F59E0B; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">Satisfaction</p>
        @if($stats['avg_satisfaction'] > 0)
        <p style="font-size:2.2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ number_format($stats['avg_satisfaction'], 1) }}/5</p>
        @else
        <p style="font-size:1.2rem; font-weight:600; color:#9CA3AF; margin:.5rem 0 0;">N/A</p>
        @endif
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:2rem;">

    {{-- Répartition par type --}}
    <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1.25rem;">Répartition par type</h3>
        @foreach($stats['by_type'] as $type => $count)
        @php
            $total = $stats['total'] ?: 1;
            $pct = round($count / $total * 100);
            $typeLabels = ['panne' => 'Panne', 'incident' => 'Incident', 'demande' => 'Demande', 'autre' => 'Autre'];
            $typeColors = ['panne' => '#EF4444', 'incident' => '#F59E0B', 'demande' => '#889ABF', 'autre' => '#10B981'];
        @endphp
        <div style="margin-bottom:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.3rem;">
                <span style="font-size:.82rem; font-weight:600; color:#374151;">{{ $typeLabels[$type] ?? ucfirst($type) }}</span>
                <span style="font-size:.82rem; color:#6B7280;">{{ $count }} ({{ $pct }}%)</span>
            </div>
            <div style="background:#F3F4F6; border-radius:100px; height:8px; overflow:hidden;">
                <div style="width:{{ $pct }}%; background:{{ $typeColors[$type] ?? '#889ABF' }}; height:100%; border-radius:100px; transition:width .5s;"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Répartition par priorité --}}
    <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1.25rem;">Répartition par priorité</h3>
        @foreach($stats['by_priority'] as $priority => $count)
        @php
            $pct = round($count / ($stats['total'] ?: 1) * 100);
            $priorityLabels = ['critique' => 'Critique', 'haute' => 'Haute', 'normale' => 'Normale', 'faible' => 'Faible'];
            $priorityColors = ['critique' => '#EF4444', 'haute' => '#F59E0B', 'normale' => '#889ABF', 'faible' => '#10B981'];
        @endphp
        <div style="margin-bottom:1rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.3rem;">
                <span style="font-size:.82rem; font-weight:600; color:#374151;">{{ $priorityLabels[$priority] ?? ucfirst($priority) }}</span>
                <span style="font-size:.82rem; color:#6B7280;">{{ $count }} ({{ $pct }}%)</span>
            </div>
            <div style="background:#F3F4F6; border-radius:100px; height:8px; overflow:hidden;">
                <div style="width:{{ $pct }}%; background:{{ $priorityColors[$priority] ?? '#889ABF' }}; height:100%; border-radius:100px;"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Performance techniciens --}}
<div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:2rem;">
    <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1.25rem;">Performance des techniciens</h3>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:.82rem;">
            <thead>
                <tr style="border-bottom:2px solid #F3F4F6;">
                    <th style="text-align:left; padding:.75rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase;">Technicien</th>
                    <th style="text-align:center; padding:.75rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase;">Assignés</th>
                    <th style="text-align:center; padding:.75rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase;">Résolus</th>
                    <th style="text-align:center; padding:.75rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase;">Taux résolution</th>
                    <th style="text-align:center; padding:.75rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase;">Délai moyen</th>
                    <th style="text-align:center; padding:.75rem; color:#6B7280; font-weight:600; font-size:.75rem; text-transform:uppercase;">SLA respectés</th>
                </tr>
            </thead>
            <tbody>
                @forelse($technicianStats as $tech)
                <tr style="border-bottom:1px solid #F9FAFB;">
                    <td style="padding:.75rem;">
                        <div style="display:flex; align-items:center; gap:.5rem;">
                            <div style="width:32px; height:32px; background:rgba(136,154,191,.15); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; color:#889ABF;">
                                {{ strtoupper(substr($tech['name'], 0, 1)) }}
                            </div>
                            <span style="font-weight:600; color:#1A1F2E;">{{ $tech['name'] }}</span>
                        </div>
                    </td>
                    <td style="padding:.75rem; text-align:center; color:#6B7280;">{{ $tech['total_assigned'] }}</td>
                    <td style="padding:.75rem; text-align:center; color:#6B7280;">{{ $tech['total_resolved'] }}</td>
                    <td style="padding:.75rem; text-align:center;">
                        @php $rate = $tech['total_assigned'] > 0 ? round($tech['total_resolved'] / $tech['total_assigned'] * 100) : 0; @endphp
                        <span style="font-weight:600; color:{{ $rate >= 80 ? '#10B981' : ($rate >= 50 ? '#F59E0B' : '#EF4444') }};">{{ $rate }}%</span>
                    </td>
                    <td style="padding:.75rem; text-align:center; color:#6B7280;">{{ $tech['avg_resolution_hours'] ? $tech['avg_resolution_hours'].'h' : '—' }}</td>
                    <td style="padding:.75rem; text-align:center;">
                        @php $sla = $tech['total_resolved'] > 0 ? round($tech['sla_respected'] / $tech['total_resolved'] * 100) : 0; @endphp
                        <div style="display:flex; align-items:center; justify-content:center; gap:.4rem;">
                            <div style="background:#F3F4F6; border-radius:100px; height:6px; width:60px; overflow:hidden;">
                                <div style="width:{{ $sla }}%; background:{{ $sla >= 80 ? '#10B981' : ($sla >= 60 ? '#F59E0B' : '#EF4444') }}; height:100%;"></div>
                            </div>
                            <span style="font-size:.75rem; font-weight:600; color:{{ $sla >= 80 ? '#10B981' : ($sla >= 60 ? '#F59E0B' : '#EF4444') }};">{{ $sla }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center; padding:2rem; color:#9CA3AF;">Aucune donnée disponible</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Actions d'export --}}
<div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
    <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1.25rem;">Exporter les données</h3>

    <form method="GET" action="{{ route('reports.export') }}" style="display:grid; grid-template-columns:1fr 1fr 1fr auto; gap:.75rem; align-items:end;">
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase;">Période du</label>
            <input type="date" name="date_from" value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; outline:none; box-sizing:border-box; font-family:inherit;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
        </div>
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase;">Au</label>
            <input type="date" name="date_to" value="{{ now()->format('Y-m-d') }}"
                style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; outline:none; box-sizing:border-box; font-family:inherit;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
        </div>
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase;">Format</label>
            <select name="format" style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; background:#fff; font-family:inherit; cursor:pointer; outline:none;">
                <option value="csv">CSV (Excel)</option>
                <option value="pdf">PDF</option>
            </select>
        </div>
        <button type="submit" style="padding:.6rem 1.4rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.88rem; font-weight:600; cursor:pointer; font-family:inherit; display:flex; align-items:center; gap:.5rem; white-space:nowrap;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Télécharger
        </button>
    </form>
</div>
@endsection
