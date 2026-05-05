<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inventaire Parc Informatique — SENSTOCK</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size:9px; color:#1A1F2E; line-height:1.4; }

        .header { background:#1A1F2E; color:#fff; padding:16px 24px; display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
        .header h1 { font-size:16px; font-weight:700; margin-bottom:3px; }
        .header p { font-size:9px; color:#889ABF; }
        .header-right { text-align:right; }
        .header-right .company { font-size:14px; font-weight:700; color:#889ABF; }

        .kpi-row { display:table; width:100%; margin-bottom:14px; border-spacing:6px; }
        .kpi-cell { display:table-cell; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:6px; padding:10px; text-align:center; }
        .kpi-value { font-size:18px; font-weight:800; color:#1A1F2E; display:block; }
        .kpi-label { font-size:8px; color:#9CA3AF; text-transform:uppercase; font-weight:700; letter-spacing:.04em; display:block; margin-top:3px; }
        .kpi-actif .kpi-value { color:#065F46; } .kpi-actif { background:#F0FDF4; border-color:#86EFAC; }
        .kpi-maint .kpi-value { color:#92400E; } .kpi-maint { background:#FFFBEB; border-color:#FCD34D; }
        .kpi-hs .kpi-value { color:#991B1B; }    .kpi-hs { background:#FEF2F2; border-color:#FCA5A5; }
        .kpi-exp .kpi-value { color:#DC2626; }    .kpi-exp { background:#FEF2F2; border-color:#FCA5A5; }

        table { width:100%; border-collapse:collapse; margin-bottom:16px; }
        thead tr { background:#889ABF; color:#fff; }
        thead th { padding:5px 6px; text-align:left; font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; white-space:nowrap; }
        tbody tr { border-bottom:1px solid #F3F4F6; }
        tbody tr:nth-child(even) { background:#F9FAFB; }
        tbody td { padding:5px 6px; font-size:8.5px; color:#374151; vertical-align:top; }
        .mono { font-family:monospace; font-size:8px; }

        .badge { display:inline-block; padding:1px 6px; border-radius:10px; font-size:7.5px; font-weight:700; }
        .badge-actif          { background:#D1FAE5; color:#065F46; }
        .badge-en_maintenance { background:#FEF3C7; color:#92400E; }
        .badge-hors_service   { background:#FEE2E2; color:#991B1B; }
        .badge-en_stock       { background:#DBEAFE; color:#1E40AF; }
        .badge-retire         { background:#F3F4F6; color:#374151; }

        .garantie-ok  { color:#16A34A; font-weight:600; }
        .garantie-exp { color:#DC2626; font-weight:600; }
        .garantie-soon{ color:#D97706; font-weight:600; }

        .footer { padding:10px 24px; background:#F9FAFB; border-top:2px solid #889ABF; display:flex; justify-content:space-between; margin-top:8px; }
        .footer p { font-size:8px; color:#9CA3AF; }

        .section-title { font-size:10px; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.06em; margin:12px 0 6px; padding-bottom:4px; border-bottom:1px solid #E5E7EB; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- En-tête --}}
<div class="header">
    <div>
        <h1>Inventaire du Parc Informatique</h1>
        <p>SENSTOCK — Sénégalaise de Stockage</p>
        <p>Généré le {{ $generatedAt->format('d/m/Y à H:i') }}</p>
    </div>
    <div class="header-right">
        <div class="company">SENSTOCK</div>
        <p style="color:#9CA3AF; margin-top:4px;">{{ $equipements->count() }} équipements au total</p>
    </div>
</div>

{{-- KPI --}}
<table style="width:100%; border-collapse:separate; border-spacing:5px; margin-bottom:14px;">
    <tr>
        <td style="background:#F0F4FF; border:1px solid #C7D2FE; border-radius:6px; padding:10px; text-align:center; width:20%;">
            <span style="font-size:18px; font-weight:800; color:#1A1F2E; display:block;">{{ $kpi['total'] }}</span>
            <span style="font-size:8px; color:#889ABF; text-transform:uppercase; font-weight:700;">Total équipements</span>
        </td>
        <td style="background:#F0FDF4; border:1px solid #86EFAC; border-radius:6px; padding:10px; text-align:center;">
            <span style="font-size:18px; font-weight:800; color:#065F46; display:block;">{{ $kpi['actifs'] }}</span>
            <span style="font-size:8px; color:#10B981; text-transform:uppercase; font-weight:700;">Actifs</span>
        </td>
        <td style="background:#FFFBEB; border:1px solid #FCD34D; border-radius:6px; padding:10px; text-align:center;">
            <span style="font-size:18px; font-weight:800; color:#92400E; display:block;">{{ $kpi['en_maintenance'] }}</span>
            <span style="font-size:8px; color:#F59E0B; text-transform:uppercase; font-weight:700;">En maintenance</span>
        </td>
        <td style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:6px; padding:10px; text-align:center;">
            <span style="font-size:18px; font-weight:800; color:#991B1B; display:block;">{{ $kpi['hors_service'] }}</span>
            <span style="font-size:8px; color:#EF4444; text-transform:uppercase; font-weight:700;">Hors service</span>
        </td>
        <td style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:6px; padding:10px; text-align:center;">
            <span style="font-size:18px; font-weight:800; color:#DC2626; display:block;">{{ $kpi['garantie_expire'] }}</span>
            <span style="font-size:8px; color:#DC2626; text-transform:uppercase; font-weight:700;">Garantie expirée</span>
        </td>
    </tr>
</table>

{{-- Tableau inventaire --}}
<p class="section-title">Inventaire complet</p>
<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Nom / Désignation</th>
            <th>Catégorie</th>
            <th>Marque / Modèle</th>
            <th>N° Série</th>
            <th>IP</th>
            <th>Statut</th>
            <th>Affecté à</th>
            <th>Localisation</th>
            <th>Garantie</th>
            <th>Prix (FCFA)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($equipements as $eq)
        <tr>
            <td class="mono" style="color:#889ABF; font-weight:700;">{{ $eq->code_inventaire }}</td>
            <td style="font-weight:600; max-width:120px;">
                {{ $eq->nom }}
            </td>
            <td>{{ $eq->category_label }}</td>
            <td>{{ trim($eq->marque . ' ' . $eq->modele) ?: '—' }}</td>
            <td class="mono">{{ $eq->numero_serie ?? '—' }}</td>
            <td class="mono">{{ $eq->adresse_ip ?? '—' }}</td>
            <td>
                <span class="badge badge-{{ $eq->statut }}">{{ $eq->statut_label }}</span>
            </td>
            <td>{{ $eq->assignedUser?->name ?? '—' }}</td>
            <td>{{ $eq->localisation ?? '—' }}</td>
            <td>
                @if($eq->fin_garantie)
                    @if($eq->fin_garantie->isPast())
                        <span class="garantie-exp">Expirée {{ $eq->fin_garantie->format('d/m/Y') }}</span>
                    @elseif($eq->fin_garantie->diffInDays(now()) <= 30)
                        <span class="garantie-soon">Bientôt {{ $eq->fin_garantie->format('d/m/Y') }}</span>
                    @else
                        <span class="garantie-ok">{{ $eq->fin_garantie->format('d/m/Y') }}</span>
                    @endif
                @else
                    <span style="color:#9CA3AF;">—</span>
                @endif
            </td>
            <td style="text-align:right; white-space:nowrap;">
                {{ $eq->prix_achat ? number_format($eq->prix_achat, 0, ',', ' ') : '—' }}
            </td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center; padding:20px; color:#9CA3AF;">Aucun équipement</td></tr>
        @endforelse
    </tbody>
</table>

{{-- Spécifications techniques (page 2) --}}
@php $avecSpecs = $equipements->filter(fn($e) => !empty($e->specifications)); @endphp
@if($avecSpecs->count())
<div class="page-break">
    <div class="header" style="margin-bottom:16px;">
        <div>
            <h1>Spécifications Techniques</h1>
            <p>SENSTOCK — Parc Informatique</p>
        </div>
        <div class="header-right">
            <div class="company">SENSTOCK</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Processeur (CPU)</th>
                <th>RAM</th>
                <th>Stockage</th>
                <th>Système d'exploitation</th>
                <th>Affecté à</th>
                <th>Département</th>
            </tr>
        </thead>
        <tbody>
            @foreach($avecSpecs as $eq)
            @php $specs = $eq->specifications ?? []; @endphp
            <tr>
                <td class="mono" style="color:#889ABF; font-weight:700;">{{ $eq->code_inventaire }}</td>
                <td style="font-weight:600;">{{ $eq->nom }}</td>
                <td>{{ $eq->category_label }}</td>
                <td>{{ $specs['cpu'] ?? '—' }}</td>
                <td>{{ $specs['ram'] ?? '—' }}</td>
                <td>{{ $specs['stockage'] ?? '—' }}</td>
                <td>{{ $specs['os'] ?? '—' }}</td>
                <td>{{ $eq->assignedUser?->name ?? '—' }}</td>
                <td>{{ $eq->departement ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Pied de page --}}
<div class="footer">
    <p>Document confidentiel — Usage interne SENSTOCK uniquement</p>
    <p>© {{ now()->year }} <strong style="color:#889ABF;">SENSTOCK</strong> — Généré via plateforme ITSM</p>
</div>

</body>
</html>
