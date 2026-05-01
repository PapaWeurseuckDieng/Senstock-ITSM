{{-- resources/views/emails/ticket-assigned.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ticket assigné — SENSTOCK ITSM</title>
<style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#F0F2F8;margin:0;padding:20px;}
    .wrapper{max-width:580px;margin:0 auto;}
    .header{background:#1A1F2E;border-radius:12px 12px 0 0;padding:24px 32px;text-align:center;}
    .header h1{color:white;font-size:18px;margin:10px 0 0;}
    .body{background:white;padding:28px 32px;}
    .ticket-ref{background:#EEF1F7;border-radius:8px;padding:14px;margin-bottom:20px;text-align:center;}
    .number{font-family:'Courier New',monospace;font-size:20px;font-weight:700;color:#6878A0;}
    .section{margin-bottom:16px;}
    .label{font-size:11px;font-weight:700;color:#9BA3B8;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:5px;}
    .value{font-size:14px;color:#2D3348;}
    .cta{text-align:center;margin:24px 0;}
    .btn{display:inline-block;padding:12px 26px;background:#889ABF;color:white;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;}
    .footer{background:#F8F9FC;border-radius:0 0 12px 12px;padding:16px 32px;text-align:center;color:#9BA3B8;font-size:12px;border-top:1px solid #DDE1EC;}
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>👤 Ticket assigné</h1>
    </div>
    <div class="body">
        <p style="font-size:14px;color:#5A6380;margin-bottom:20px;">
            Bonjour <strong>{{ $ticket->assignee->name }}</strong>,<br>
            Le ticket suivant vous a été assigné. Merci de le traiter dans les délais SLA.
        </p>
        <div class="ticket-ref">
            <div class="number">{{ $ticket->ticket_number }}</div>
        </div>
        <div class="section"><div class="label">Titre</div><div class="value" style="font-weight:600;">{{ $ticket->title }}</div></div>
        <div class="section"><div class="label">Demandeur</div><div class="value">{{ $ticket->user->name }}</div></div>
        <div class="section"><div class="label">Priorité</div><div class="value">{{ $ticket->priority_label }}</div></div>
        <div class="section"><div class="label">Échéance SLA</div><div class="value" style="color:#E85555;font-weight:600;">{{ $ticket->sla_deadline?->format('d/m/Y à H:i') }}</div></div>
        <div class="cta"><a href="{{ route('tickets.show', $ticket) }}" class="btn">Traiter ce ticket →</a></div>
    </div>
    <div class="footer">SENSTOCK ITSM © {{ date('Y') }}</div>
</div>
</body>
</html>
