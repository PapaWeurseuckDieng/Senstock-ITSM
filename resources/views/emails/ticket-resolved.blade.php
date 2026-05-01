{{-- ticket-resolved.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Ticket résolu — SENSTOCK ITSM</title>
<style>
    body{font-family:'Segoe UI',Arial,sans-serif;background:#F0F2F8;margin:0;padding:20px;}
    .wrapper{max-width:580px;margin:0 auto;}
    .header{background:#3DBB8A;border-radius:12px 12px 0 0;padding:24px 32px;text-align:center;}
    .header h1{color:white;font-size:18px;margin:0;}
    .body{background:white;padding:28px 32px;}
    .ticket-ref{background:#EBF9F4;border-radius:8px;padding:14px;margin-bottom:20px;text-align:center;border:1px solid #b6e8d6;}
    .number{font-family:'Courier New',monospace;font-size:20px;font-weight:700;color:#3DBB8A;}
    .resolution{background:#F8F9FC;border-left:4px solid #3DBB8A;padding:14px 16px;border-radius:0 8px 8px 0;margin:16px 0;font-size:13px;color:#5A6380;line-height:1.6;}
    .section{margin-bottom:16px;}
    .label{font-size:11px;font-weight:700;color:#9BA3B8;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:5px;}
    .value{font-size:14px;color:#2D3348;}
    .cta{text-align:center;margin:24px 0;}
    .btn{display:inline-block;padding:12px 26px;background:#3DBB8A;color:white;text-decoration:none;border-radius:8px;font-size:14px;font-weight:600;}
    .footer{background:#F8F9FC;border-radius:0 0 12px 12px;padding:16px 32px;text-align:center;color:#9BA3B8;font-size:12px;border-top:1px solid #DDE1EC;}
</style>
</head>
<body>
<div class="wrapper">
    <div class="header"><h1>✅ Votre ticket a été résolu</h1></div>
    <div class="body">
        <p style="font-size:14px;color:#5A6380;margin-bottom:20px;">
            Bonjour <strong>{{ $ticket->user->name }}</strong>,<br>
            Votre demande a été traitée. Merci de valider la résolution dans la plateforme.
        </p>
        <div class="ticket-ref"><div class="number">{{ $ticket->ticket_number }}</div></div>
        <div class="section"><div class="label">Titre</div><div class="value" style="font-weight:600;">{{ $ticket->title }}</div></div>
        @if($ticket->resolution_note)
        <div class="section">
            <div class="label">Solution apportée</div>
            <div class="resolution">{{ Str::limit($ticket->resolution_note, 400) }}</div>
        </div>
        @endif
        <div class="section"><div class="label">Résolu par</div><div class="value">{{ $ticket->assignee?->name ?? 'Service IT' }}</div></div>
        <div class="section"><div class="label">Date de résolution</div><div class="value">{{ $ticket->t4_resolved_at?->format('d/m/Y à H:i') }}</div></div>
        <div class="cta"><a href="{{ route('tickets.show', $ticket) }}" class="btn">Valider la résolution →</a></div>
        <p style="font-size:12px;color:#9BA3B8;text-align:center;">Si le problème persiste, réouvrez le ticket ou créez-en un nouveau.</p>
    </div>
    <div class="footer">SENSTOCK ITSM © {{ date('Y') }}</div>
</div>
</body>
</html>
