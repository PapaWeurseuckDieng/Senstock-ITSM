<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport ITSM — SENSTOCK</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #1A1F2E; line-height: 1.5; }

        .header { background: #1A1F2E; color: #fff; padding: 24px 32px; display: flex; justify-content: space-between; align-items: center; }
        .header-left h1 { font-size: 18px; font-weight: 700; margin-bottom: 4px; }
        .header-left p { font-size: 11px; color: #889ABF; }
        .header-right { text-align: right; }
        .header-right p { font-size: 10px; color: #9CA3AF; }

        .section { padding: 20px 32px; border-bottom: 1px solid #F3F4F6; }
        .section-title { font-size: 13px; font-weight: 700; color: #889ABF; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px; padding-bottom: 8px; border-bottom: 2px solid #F3F4F6; }

        .kpi-grid { display: table; width: 100%; margin-bottom: 4px; }
        .kpi-item { display: table-cell; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px; text-align: center; width: 16.6%; }
        .kpi-label { font-size: 9px; color: #889ABF; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 4px; }
        .kpi-value { font-size: 20px; font-weight: 800; color: #1A1F2E; }
        .kpi-sub { font-size: 9px; color: #9CA3AF; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead tr { background: #889ABF; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; }
        tbody tr { border-bottom: 1px solid #F3F4F6; }
        tbody tr:nth-child(even) { background: #F9FAFB; }
        tbody td { padding: 7px 10px; font-size: 10px; color: #374151; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: 700; }
        .badge-critique { background: #FEE2E2; color: #DC2626; }
        .badge-haute { background: #FEF3C7; color: #D97706; }
        .badge-normale { background: #DBEAFE; color: #2563EB; }
        .badge-faible { background: #D1FAE5; color: #065F46; }
        .badge-resolu { background: #D1FAE5; color: #065F46; }
        .badge-en_cours { background: #DBEAFE; color: #2563EB; }
        .badge-ouvert { background: #F3F4F6; color: #374151; }
        .badge-ferme { background: #F3F4F6; color: #374151; }
        .badge-sla { background: #FEE2E2; color: #DC2626; }
        .badge-ok { background: #D1FAE5; color: #065F46; }

        .progress-bar { background: #F3F4F6; border-radius: 4px; height: 6px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 4px; }

        .footer { padding: 16px 32px; background: #F9FAFB; border-top: 2px solid #889ABF; display: flex; justify-content: space-between; }
        .footer p { font-size: 9px; color: #9CA3AF; }
        .footer strong { color: #889ABF; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- En-tête --}}
<div class="header">
    <div class="header-left">
        <h1>Rapport ITSM</h1>
        <p>Plateforme de gestion des services informatiques — SENSTOCK</p>
    </div>
    <div class="header-right">
        <p style="font-size:13px; font-weight:700; color:#889ABF; margin-bottom:4px;">SENSTOCK</p>
        <p>Sénégalaise de Stockage</p>
        <p>Période : {{ $dateFrom->format('d/m/Y') }} → {{ $dateTo->format('d/m/Y') }}</p>
        <p>Généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</div>

{{-- KPI Synthèse --}}
<div class="section">
    <p class="section-title">Indicateurs clés de performance</p>
    <table style="width:100%; border-collapse:separate; border-spacing:6px;">
        <tr>
            <td style="background:#F0F4FF; border:1px solid #C7D2FE; border-radius:8px; padding:12px; text-align:center; width:16.6%;">
                <p class="kpi-label">Total tickets</p>
                <p class="kpi-value">{{ $stats['total'] }}</p>
            </td>
            <td style="background:#F0FDF4; border:1px solid #86EFAC; border-radius:8px; padding:12px; text-align:center;">
                <p class="kpi-label" style="color:#16A34A;">Résolus</p>
                <p class="kpi-value" style="color:#16A34A;">{{ $stats['resolved'] }}</p>
                <p class="kpi-sub">{{ $stats['total'] > 0 ? round($stats['resolved'] / $stats['total'] * 100) : 0 }}%</p>
            </td>
            <td style="background:#FFFBEB; border:1px solid #FCD34D; border-radius:8px; padding:12px; text-align:center;">
                <p class="kpi-label" style="color:#D97706;">En cours</p>
                <p class="kpi-value" style="color:#D97706;">{{ $stats['in_progress'] }}</p>
            </td>
            <td style="background:{{ $stats['sla_rate'] >= 80 ? '#F0FDF4' : '#FEF2F2' }}; border:1px solid {{ $stats['sla_rate'] >= 80 ? '#86EFAC' : '#FCA5A5' }}; border-radius:8px; padding:12px; text-align:center;">
                <p class="kpi-label" style="color:{{ $stats['sla_rate'] >= 80 ? '#16A34A' : '#DC2626' }};">Taux SLA</p>
                <p class="kpi-value" style="color:{{ $stats['sla_rate'] >= 80 ? '#16A34A' : '#DC2626' }};">{{ $stats['sla_rate'] }}%</p>
                <p class="kpi-sub">Objectif ≥ 80%</p>
            </td>
            <td style="background:#F5F3FF; border:1px solid #C4B5FD; border-radius:8px; padding:12px; text-align:center;">
                <p class="kpi-label" style="color:#7C3AED;">Délai moyen</p>
                <p class="kpi-value" style="color:#7C3AED;">{{ $stats['avg_resolution_hours'] }}h</p>
                <p class="kpi-sub">de résolution</p>
            </td>
            <td style="background:#FFFBEB; border:1px solid #FCD34D; border-radius:8px; padding:12px; text-align:center;">
                <p class="kpi-label" style="color:#D97706;">Satisfaction</p>
                @if($stats['avg_satisfaction'] > 0)
                <p class="kpi-value" style="color:#D97706;">{{ number_format($stats['avg_satisfaction'], 1) }}/5</p>
                @else
                <p class="kpi-value" style="color:#9CA3AF; font-size:14px;">N/A</p>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- Liste des tickets --}}
<div class="section">
    <p class="section-title">Détail des tickets ({{ $dateFrom->format('d/m/Y') }} — {{ $dateTo->format('d/m/Y') }})</p>
    <table>
        <thead>
            <tr>
                <th>Référence</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Priorité</th>
                <th>Statut</th>
                <th>Demandeur</th>
                <th>Technicien</th>
                <th>SLA</th>
                <th>Créé le</th>
                <th>Résolu le</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr>
                <td style="font-family:monospace; color:#889ABF; font-weight:700; font-size:9px;">{{ $ticket->ticket_number }}</td>
                <td style="max-width:140px; overflow:hidden;">{{ \Str::limit($ticket->title, 35) }}</td>
                <td>{{ $ticket->type_label }}</td>
                <td><span class="badge badge-{{ $ticket->priority }}">{{ $ticket->priority_label }}</span></td>
                <td><span class="badge badge-{{ $ticket->status }}">{{ $ticket->status_label }}</span></td>
                <td>{{ $ticket->user?->name ?? '—' }}</td>
                <td>{{ $ticket->assignee->name ?? '—' }}</td>
                <td>
                    @if($ticket->sla_breached)
                        <span class="badge badge-sla">Dépassé</span>
                    @else
                        <span class="badge badge-ok">OK</span>
                    @endif
                </td>
                <td style="font-size:9px; white-space:nowrap;">{{ $ticket->created_at->format('d/m/Y') }}</td>
                <td style="font-size:9px; white-space:nowrap;">{{ $ticket->t5_resolved_at ? $ticket->t5_resolved_at->format('d/m/Y') : '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center; padding:20px; color:#9CA3AF;">Aucun ticket sur cette période</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Performance techniciens --}}
@if($technicianStats->count() > 0)
<div class="section page-break">
    <p class="section-title">Performance des techniciens</p>
    <table>
        <thead>
            <tr>
                <th>Technicien</th>
                <th style="text-align:center;">Assignés</th>
                <th style="text-align:center;">Résolus</th>
                <th style="text-align:center;">Taux résolution</th>
                <th style="text-align:center;">Délai moyen</th>
                <th style="text-align:center;">SLA respectés</th>
            </tr>
        </thead>
        <tbody>
            @foreach($technicianStats as $tech)
            @php
                $resolRate = $tech['total_assigned'] > 0 ? round($tech['total_resolved'] / $tech['total_assigned'] * 100) : 0;
                $slaRate = $tech['total_resolved'] > 0 ? round($tech['sla_respected'] / $tech['total_resolved'] * 100) : 0;
            @endphp
            <tr>
                <td style="font-weight:600;">{{ $tech['name'] }}</td>
                <td style="text-align:center;">{{ $tech['total_assigned'] }}</td>
                <td style="text-align:center;">{{ $tech['total_resolved'] }}</td>
                <td style="text-align:center; color:{{ $resolRate >= 80 ? '#16A34A' : ($resolRate >= 50 ? '#D97706' : '#DC2626') }}; font-weight:700;">{{ $resolRate }}%</td>
                <td style="text-align:center;">{{ $tech['avg_resolution_hours'] ? $tech['avg_resolution_hours'].'h' : '—' }}</td>
                <td style="text-align:center; color:{{ $slaRate >= 80 ? '#16A34A' : ($slaRate >= 60 ? '#D97706' : '#DC2626') }}; font-weight:700;">{{ $slaRate }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Pied de page --}}
<div class="footer">
    <p>Rapport généré automatiquement par la plateforme ITSM SENSTOCK — Confidentiel</p>
    <p>© {{ now()->year }} <strong>SENSTOCK</strong> — Sénégalaise de Stockage</p>
</div>

</body>
</html>
