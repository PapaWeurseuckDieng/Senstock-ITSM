<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nouveau ticket — SENSTOCK ITSM</title>
<style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #F0F2F8; margin: 0; padding: 20px; }
    .wrapper { max-width: 600px; margin: 0 auto; }
    .header { background: #1A1F2E; border-radius: 12px 12px 0 0; padding: 28px 32px; text-align: center; }
    .header img { height: 48px; object-fit: contain; background: white; border-radius: 8px; padding: 4px 8px; }
    .header h1 { color: white; font-size: 20px; margin: 14px 0 4px; }
    .header p { color: #889ABF; font-size: 13px; margin: 0; }
    .body { background: white; padding: 32px; }
    .ticket-ref { background: #EEF1F7; border-radius: 8px; padding: 16px; margin-bottom: 24px; text-align: center; }
    .ticket-ref .number { font-family: 'Courier New', monospace; font-size: 22px; font-weight: 700; color: #6878A0; }
    .ticket-ref .sub { font-size: 12px; color: #9BA3B8; margin-top: 4px; }
    .section { margin-bottom: 20px; }
    .section-title { font-size: 11px; font-weight: 700; color: #9BA3B8; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px; }
    .section-value { font-size: 14px; color: #2D3348; line-height: 1.6; }
    .badges { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-critique { background: #FDEEEE; color: #E85555; }
    .badge-haute    { background: #FEF5E7; color: #F5A623; }
    .badge-normale  { background: #EBF4FD; color: #4A90D9; }
    .badge-panne    { background: #FDEEEE; color: #E85555; }
    .badge-incident { background: #FEF5E7; color: #F5A623; }
    .badge-demande  { background: #EEF1F7; color: #6878A0; }
    .alert { background: #FEF5E7; border-left: 4px solid #F5A623; padding: 14px 16px; border-radius: 0 8px 8px 0; margin-bottom: 20px; font-size: 13px; color: #5A6380; }
    .alert.critical { background: #FDEEEE; border-left-color: #E85555; }
    .cta { text-align: center; margin: 28px 0; }
    .btn { display: inline-block; padding: 13px 28px; background: #889ABF; color: white; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; }
    .footer { background: #F8F9FC; border-radius: 0 0 12px 12px; padding: 18px 32px; text-align: center; color: #9BA3B8; font-size: 12px; border-top: 1px solid #DDE1EC; }
    .divider { border: none; border-top: 1px solid #DDE1EC; margin: 20px 0; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <img src="{{ config('app.url') }}/images/logo.jpg" alt="SENSTOCK">
        <h1>
            @if($ticket->type === 'panne') ⚡ Panne signalée
            @elseif($ticket->type === 'incident') 🔴 Incident signalé
            @else 📋 Nouveau ticket IT
            @endif
        </h1>
        <p>Plateforme ITSM — SENSTOCK</p>
    </div>

    <div class="body">
        @if($ticket->type === 'panne' || $ticket->priority === 'critique')
        <div class="alert critical">
            <strong>⚠️ Attention :</strong> Ce ticket nécessite une intervention urgente (priorité {{ $ticket->priority_label }}).
        </div>
        @endif

        <div class="ticket-ref">
            <div class="number">{{ $ticket->ticket_number }}</div>
            <div class="sub">Référence à conserver</div>
        </div>

        <div class="badges">
            <span class="badge badge-{{ $ticket->type }}">{{ $ticket->type_label }}</span>
            <span class="badge badge-{{ $ticket->priority }}">Priorité {{ $ticket->priority_label }}</span>
            <span class="badge" style="background:#EBF9F4;color:#3DBB8A;">SLA : {{ \App\Models\Ticket::SLA_HOURS[$ticket->priority] ?? 24 }}h</span>
        </div>

        <div class="section">
            <div class="section-title">Titre</div>
            <div class="section-value" style="font-weight:600;">{{ $ticket->title }}</div>
        </div>

        <div class="section">
            <div class="section-title">Description</div>
            <div class="section-value">{{ Str::limit($ticket->description, 500) }}</div>
        </div>

        <hr class="divider">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="section">
                <div class="section-title">Demandeur</div>
                <div class="section-value">{{ $ticket->user->name }}</div>
            </div>
            <div class="section">
                <div class="section-title">Catégorie</div>
                <div class="section-value">{{ ucfirst($ticket->category) }}</div>
            </div>
            <div class="section">
                <div class="section-title">Date de création</div>
                <div class="section-value">{{ $ticket->t0_created_at?->format('d/m/Y à H:i') }}</div>
            </div>
            <div class="section">
                <div class="section-title">Échéance SLA</div>
                <div class="section-value" style="color:#E85555;font-weight:600;">{{ $ticket->sla_deadline?->format('d/m/Y à H:i') }}</div>
            </div>
        </div>

        <div class="cta">
            <a href="{{ route('tickets.show', $ticket) }}" class="btn">
                Voir et traiter ce ticket →
            </a>
        </div>
    </div>

    <div class="footer">
        Ce message a été envoyé automatiquement par la plateforme ITSM de SENSTOCK.<br>
        © {{ date('Y') }} SENSTOCK — Sénégalaise de Stockage
    </div>
</div>
</body>
</html>
