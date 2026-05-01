<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>SLA Dépassé — SENSTOCK ITSM</title>
<style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#F0F2F8;margin:0;padding:20px;}
    .wrapper{max-width:580px;margin:0 auto;}
    .header{background:#E85555;border-radius:12px 12px 0 0;padding:24px 32px;text-align:center;}
    .header h1{color:white;font-size:18px;margin:0;}
    .body{background:white;padding:28px 32px;}
    .alert-box{background:#FDEEEE;border:1px solid #f5b0b0;border-radius:8px;padding:16px;margin-bottom:20px;text-align:center;}
    .alert-box .icon{font-size:32px;display:block;margin-bottom:8px;}
    .alert-box p{color:#9b2c2c;font-size:13px;margin:0;line-height:1.5;}
    .ticket-ref{background:#EEF1F7;border-radius:8px;padding:14px;margin-bottom:20px;text-align:center;}
    .number{font-family:'Courier New',monospace;font-size:20px;font-weight:700;color:#E85555;}
    .section{margin-bottom:16px;}
    .label{font-size:11px;font-weight:700;color:#9BA3B8;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:5px;}
    .value{font-size:14px;color:#2D3348;}
    .cta{text-align:center;margin:24px 0;}
    .btn{display:inline-block;padding:12px 26px;background:#E85555;color:white;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;}
    .footer{background:#F8F9FC;border-radius:0 0 12px 12px;padding:16px 32px;text-align:center;color:#9BA3B8;font-size:12px;border-top:1px solid #DDE1EC;}
</style>
</head>
<body>
<div class="wrapper">
    <div class="header"><h1>⚠️ Alerte SLA — Ticket en retard</h1></div>
    <div class="body">
        <div class="alert-box">
            <span class="icon">🚨</span>
            <p><strong>Le délai SLA de ce ticket a été dépassé.</strong><br>Une intervention immédiate est requise.</p>
        </div>
        <div class="ticket-ref"><div class="number">{{ $ticket->ticket_number }}</div></div>
        <div class="section"><div class="label">Titre</div><div class="value" style="font-weight:600;">{{ $ticket->title }}</div></div>
        <div class="section"><div class="label">Priorité</div><div class="value">{{ $ticket->priority_label }}</div></div>
        <div class="section"><div class="label">Demandeur</div><div class="value">{{ $ticket->user->name }}</div></div>
        <div class="section"><div class="label">Assigné à</div><div class="value">{{ $ticket->assignee?->name ?? 'Non assigné' }}</div></div>
        <div class="section"><div class="label">Échéance SLA dépassée le</div><div class="value" style="color:#E85555;font-weight:600;">{{ $ticket->sla_deadline?->format('d/m/Y à H:i') }}</div></div>
        <div class="section"><div class="label">Dépassement</div><div class="value" style="color:#E85555;font-weight:600;">{{ $ticket->sla_deadline?->diffForHumans() }}</div></div>
        <div class="cta"><a href="{{ route('tickets.show', $ticket) }}" class="btn">Traiter ce ticket maintenant →</a></div>
    </div>
    <div class="footer">SENSTOCK ITSM — Alerte automatique © {{ date('Y') }}</div>
</div>
</body>
</html>
