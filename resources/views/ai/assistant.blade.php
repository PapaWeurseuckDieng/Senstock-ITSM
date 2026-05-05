@extends('layouts.app')

@section('title', 'Assistant IA — SENIA')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:.75rem;">
            <span style="width:36px; height:36px; background:linear-gradient(135deg,#889ABF,#5B6FA8); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a4 4 0 014 4 4 4 0 01-4 4 4 4 0 01-4-4 4 4 0 014-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4z"/></svg>
            </span>
            SENIA — Assistant IA
        </h1>
        <p class="page-subtitle">Intelligence artificielle intégrée à votre plateforme ITSM</p>
    </div>
</div>

{{-- Navigation onglets --}}
<div style="display:flex; gap:.5rem; margin-bottom:1.5rem; background:#fff; padding:.5rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06);">
    @foreach([
        ['id'=>'chat',       'label'=>'Chat IA',         'icon'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
        ['id'=>'ticket',     'label'=>'Créer un ticket', 'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['id'=>'reponse',    'label'=>'Suggérer réponse','icon'=>'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6'],
        ['id'=>'kpi',        'label'=>'Analyser KPI',    'icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
    ] as $tab)
    <button onclick="showTab('{{ $tab['id'] }}')" id="tab-{{ $tab['id'] }}"
        style="display:flex; align-items:center; gap:.5rem; padding:.6rem 1.1rem; border-radius:8px; border:none; cursor:pointer; font-size:.84rem; font-weight:600; font-family:inherit; transition:all .15s; flex:1; justify-content:center;"
        class="ai-tab">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="{{ $tab['icon'] }}"/></svg>
        {{ $tab['label'] }}
    </button>
    @endforeach
</div>

{{-- ─── ONGLET CHAT ─────────────────────────────────────────────────────── --}}
<div id="panel-chat" class="ai-panel">
    <div style="display:grid; grid-template-columns:1fr 300px; gap:1.5rem;">
        <div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); display:flex; flex-direction:column; height:560px;">
            {{-- Messages --}}
            <div id="chat-messages" style="flex:1; overflow-y:auto; padding:1.25rem; display:flex; flex-direction:column; gap:.75rem;">
                <div style="display:flex; gap:.6rem; align-items:flex-start;">
                    <div style="width:32px; height:32px; background:linear-gradient(135deg,#889ABF,#5B6FA8); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a4 4 0 014 4 4 4 0 01-4 4 4 4 0 01-4-4 4 4 0 014-4z"/></svg>
                    </div>
                    <div style="background:#F0F4FF; border-radius:0 10px 10px 10px; padding:.75rem 1rem; max-width:85%; font-size:.85rem; color:#374151; line-height:1.6;">
                        Bonjour <strong>{{ auth()->user()->name }}</strong> ! Je suis <strong>SENIA</strong>, votre assistant IA ITSM.<br>
                        Je peux vous aider à créer des tickets, analyser vos performances, chercher des solutions techniques et bien plus. Comment puis-je vous aider ?
                    </div>
                </div>
            </div>
            {{-- Saisie --}}
            <div style="padding:1rem; border-top:1px solid #F3F4F6;">
                <div style="display:flex; gap:.6rem; align-items:flex-end;">
                    <textarea id="chat-input" rows="2" placeholder="Posez votre question à SENIA…"
                        style="flex:1; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; font-family:inherit; resize:none; outline:none; line-height:1.5;"
                        onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'"
                        onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();envoyerChat()}"></textarea>
                    <button onclick="envoyerChat()"
                        style="padding:.65rem 1.2rem; background:#889ABF; color:#fff; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-family:inherit; font-size:.88rem; flex-shrink:0;"
                        onmouseover="this.style.background='#7180A8'" onmouseout="this.style.background='#889ABF'">
                        Envoyer
                    </button>
                </div>
                <p style="font-size:.72rem; color:#9CA3AF; margin:.4rem 0 0;">Entrée pour envoyer · Shift+Entrée pour saut de ligne</p>
            </div>
        </div>

        {{-- Suggestions rapides --}}
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .75rem;">Questions rapides</h3>
                @foreach([
                    'Comment créer un ticket urgent ?',
                    'Quel est le délai SLA critique ?',
                    'Comment réinitialiser un mot de passe ?',
                    'Mon imprimante ne répond plus',
                    'Analyser mes performances ce mois',
                    'Problème de connexion réseau',
                ] as $q)
                <button onclick="setQuestion('{{ $q }}')"
                    style="width:100%; text-align:left; padding:.5rem .75rem; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:8px; font-size:.78rem; color:#374151; cursor:pointer; margin-bottom:.4rem; font-family:inherit; transition:all .15s;"
                    onmouseover="this.style.background='#F0F4FF';this.style.borderColor='#889ABF'"
                    onmouseout="this.style.background='#F9FAFB';this.style.borderColor='#E5E7EB'">
                    {{ $q }}
                </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ─── ONGLET CRÉER TICKET ─────────────────────────────────────────────── --}}
<div id="panel-ticket" class="ai-panel" style="display:none;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem;">Décrivez votre problème</h3>
            <textarea id="ticket-description" rows="8" placeholder="Décrivez votre problème en détail…&#10;Ex: Mon ordinateur portable ne démarre plus depuis ce matin. J'entends un bip et l'écran reste noir."
                style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; font-family:inherit; resize:vertical; outline:none; box-sizing:border-box;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'"></textarea>
            <button onclick="analyserTicket()"
                style="width:100%; margin-top:.75rem; padding:.75rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.9rem; font-weight:700; cursor:pointer; font-family:inherit; display:flex; align-items:center; justify-content:center; gap:.5rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                Analyser avec l'IA
            </button>
        </div>

        <div id="ticket-result" style="display:none; background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem; display:flex; align-items:center; gap:.5rem;">
                <svg width="16" height="16" fill="none" stroke="#10B981" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Analyse IA
            </h3>
            <div id="ticket-result-content"></div>
            <a id="ticket-create-link" href="{{ route('tickets.create') }}" target="_blank"
                style="display:flex; align-items:center; justify-content:center; gap:.5rem; margin-top:1rem; padding:.65rem; background:#889ABF; color:#fff; border-radius:8px; font-size:.85rem; font-weight:600; text-decoration:none;">
                Créer ce ticket →
            </a>
        </div>

        <div id="ticket-loading" style="display:none; background:#fff; border-radius:12px; padding:2rem; box-shadow:0 2px 8px rgba(0,0,0,.06); align-items:center; justify-content:center; flex-direction:column; gap:1rem;">
            <div style="width:40px; height:40px; border:3px solid #E5E7EB; border-top-color:#889ABF; border-radius:50%; animation:spin 1s linear infinite;"></div>
            <p style="font-size:.85rem; color:#6B7280; font-weight:500;">Analyse en cours…</p>
        </div>
    </div>
</div>

{{-- ─── ONGLET SUGGÉRER RÉPONSE ─────────────────────────────────────────── --}}
<div id="panel-reponse" class="ai-panel" style="display:none;">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem;">Sélectionner un ticket</h3>
            <select id="reponse-ticket-id"
                style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; background:#fff; font-family:inherit; cursor:pointer; outline:none; margin-bottom:1rem;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                <option value="">— Choisir un ticket ouvert —</option>
                @foreach(\App\Models\Ticket::whereNotIn('status',['ferme','annule'])->with('user')->orderBy('created_at','desc')->limit(30)->get() as $t)
                <option value="{{ $t->id }}">[{{ strtoupper($t->priority) }}] {{ $t->ticket_number }} — {{ Str::limit($t->title, 50) }}</option>
                @endforeach
            </select>
            <button onclick="suggererReponse()"
                style="width:100%; padding:.75rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.9rem; font-weight:700; cursor:pointer; font-family:inherit;">
                Générer une réponse IA
            </button>
        </div>

        <div id="reponse-result" style="display:none; background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem;">Réponse suggérée</h3>
            <div id="reponse-result-content"></div>
        </div>

        <div id="reponse-loading" style="display:none; background:#fff; border-radius:12px; padding:2rem; box-shadow:0 2px 8px rgba(0,0,0,.06); align-items:center; justify-content:center; flex-direction:column; gap:1rem;">
            <div style="width:40px; height:40px; border:3px solid #E5E7EB; border-top-color:#889ABF; border-radius:50%; animation:spin 1s linear infinite;"></div>
            <p style="font-size:.85rem; color:#6B7280; font-weight:500;">Génération en cours…</p>
        </div>
    </div>
</div>

{{-- ─── ONGLET KPI ──────────────────────────────────────────────────────── --}}
<div id="panel-kpi" class="ai-panel" style="display:none;">
    <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
            <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 .25rem;">Analyse IA des performances</h3>
            <p style="font-size:.8rem; color:#9CA3AF; margin:0;">L'IA analyse vos KPI en temps réel et génère des recommandations actionnables</p>
        </div>
        <button onclick="analyserKPI()"
            style="padding:.65rem 1.4rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.88rem; font-weight:700; cursor:pointer; font-family:inherit; display:flex; align-items:center; gap:.5rem;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Lancer l'analyse
        </button>
    </div>
    <div id="kpi-loading" style="display:none; background:#fff; border-radius:12px; padding:3rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center;">
        <div style="width:48px; height:48px; border:3px solid #E5E7EB; border-top-color:#889ABF; border-radius:50%; animation:spin 1s linear infinite; margin:0 auto 1rem;"></div>
        <p style="font-size:.88rem; color:#6B7280; font-weight:500;">Analyse des performances en cours…</p>
    </div>
    <div id="kpi-result" style="display:none;"></div>
</div>

<style>
.ai-tab { background:#F9FAFB; color:#6B7280; }
.ai-tab.active { background:#889ABF; color:#fff; }
@keyframes spin { to { transform:rotate(360deg); } }
.badge-ai { display:inline-block; padding:.2rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; }
</style>

<script>
const CSRF = '{{ csrf_token() }}';
let chatHistory = [];

// ─── Navigation onglets ──────────────────────────────────────────────────────
function showTab(id) {
    document.querySelectorAll('.ai-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('.ai-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('panel-' + id).style.display = 'block';
    document.getElementById('tab-' + id).classList.add('active');
}
showTab('chat');

// ─── Chat ────────────────────────────────────────────────────────────────────
function setQuestion(q) {
    document.getElementById('chat-input').value = q;
    document.getElementById('chat-input').focus();
}

async function envoyerChat() {
    const input = document.getElementById('chat-input');
    const msg = input.value.trim();
    if (!msg) return;

    appendMessage('user', msg);
    chatHistory.push({ role: 'user', content: msg });
    input.value = '';

    const typingId = appendTyping();

    try {
        const res = await fetch('{{ route("ai.chat") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ message: msg, historique: chatHistory.slice(-8) })
        });
        const data = await res.json();
        removeTyping(typingId);
        const reponse = data.reponse || 'Désolé, une erreur est survenue.';
        appendMessage('ai', reponse);
        chatHistory.push({ role: 'assistant', content: reponse });
    } catch (e) {
        removeTyping(typingId);
        appendMessage('ai', 'Erreur de connexion. Vérifiez votre clé API.');
    }
}

function appendMessage(role, content) {
    const container = document.getElementById('chat-messages');
    const isUser = role === 'user';
    const div = document.createElement('div');
    div.style.cssText = `display:flex; gap:.6rem; align-items:flex-start; ${isUser ? 'flex-direction:row-reverse' : ''}`;
    div.innerHTML = `
        <div style="width:32px; height:32px; background:${isUser ? '#1A1F2E' : 'linear-gradient(135deg,#889ABF,#5B6FA8)'}; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        </div>
        <div style="background:${isUser ? '#889ABF' : '#F0F4FF'}; color:${isUser ? '#fff' : '#374151'}; border-radius:${isUser ? '10px 0 10px 10px' : '0 10px 10px 10px'}; padding:.75rem 1rem; max-width:80%; font-size:.85rem; line-height:1.6; white-space:pre-wrap;">${escapeHtml(content)}</div>
    `;
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
    return div;
}

function appendTyping(){ return appendMessage('ai', '…'); }
function removeTyping(el){ if(el) el.remove(); }

function escapeHtml(t) {
    return t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ─── Analyse ticket ──────────────────────────────────────────────────────────
async function analyserTicket() {
    const desc = document.getElementById('ticket-description').value.trim();
    if (!desc) { alert('Veuillez saisir une description.'); return; }

    document.getElementById('ticket-result').style.display = 'none';
    document.getElementById('ticket-loading').style.display = 'flex';

    try {
        const res = await fetch('{{ route("ai.analyser-ticket") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ description: desc })
        });
        const data = await res.json();
        document.getElementById('ticket-loading').style.display = 'none';

        if (data.erreur) {
            showError('ticket-result-content', data.erreur);
        } else {
            const prioriteColors = { critique:'#EF4444', haute:'#F59E0B', normale:'#889ABF', faible:'#10B981' };
            const c = prioriteColors[data.priorite] || '#889ABF';
            document.getElementById('ticket-result-content').innerHTML = `
                <div style="margin-bottom:.75rem; padding:.75rem; background:#F9FAFB; border-radius:8px; border-left:3px solid ${c};">
                    <p style="font-size:.82rem; font-weight:700; color:#1A1F2E; margin:0 0 .3rem;">${data.titre_suggere || ''}</p>
                    <p style="font-size:.78rem; color:#6B7280; margin:0;">${data.resume || ''}</p>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem; margin-bottom:.75rem;">
                    <div style="padding:.5rem .75rem; background:#F3F4F6; border-radius:8px;">
                        <p style="font-size:.7rem; color:#9CA3AF; margin:0 0 .2rem; text-transform:uppercase;">Type</p>
                        <p style="font-size:.82rem; font-weight:600; color:#374151; margin:0;">${data.type || '—'}</p>
                    </div>
                    <div style="padding:.5rem .75rem; background:${c}20; border-radius:8px; border:1px solid ${c}40;">
                        <p style="font-size:.7rem; color:#9CA3AF; margin:0 0 .2rem; text-transform:uppercase;">Priorité</p>
                        <p style="font-size:.82rem; font-weight:700; color:${c}; margin:0;">${data.priorite || '—'} (${data.score_urgence}/10)</p>
                    </div>
                </div>
                <div style="padding:.75rem; background:#F0FDF4; border-radius:8px; border:1px solid #86EFAC;">
                    <p style="font-size:.72rem; font-weight:700; color:#065F46; margin:0 0 .3rem; text-transform:uppercase;">Solution rapide</p>
                    <p style="font-size:.82rem; color:#065F46; margin:0;">${data.solution_rapide || '—'}</p>
                </div>
            `;
        }
        document.getElementById('ticket-result').style.display = 'block';
    } catch (e) {
        document.getElementById('ticket-loading').style.display = 'none';
        showError('ticket-result-content', 'Erreur de connexion.');
        document.getElementById('ticket-result').style.display = 'block';
    }
}

// ─── Suggérer réponse ────────────────────────────────────────────────────────
async function suggererReponse() {
    const id = document.getElementById('reponse-ticket-id').value;
    if (!id) { alert('Veuillez sélectionner un ticket.'); return; }

    document.getElementById('reponse-result').style.display = 'none';
    document.getElementById('reponse-loading').style.display = 'flex';

    try {
        const res = await fetch('{{ route("ai.suggerer-reponse") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ ticket_id: id })
        });
        const data = await res.json();
        document.getElementById('reponse-loading').style.display = 'none';

        if (data.erreur) {
            showError('reponse-result-content', data.erreur);
        } else {
            const etapes = (data.etapes || []).map(e =>
                `<li style="font-size:.82rem; color:#374151; margin-bottom:.3rem;">${e}</li>`
            ).join('');
            document.getElementById('reponse-result-content').innerHTML = `
                <div style="padding:.75rem; background:#F0F4FF; border-radius:8px; margin-bottom:.75rem; border-left:3px solid #889ABF;">
                    <p style="font-size:.85rem; color:#374151; line-height:1.6; margin:0;">${data.reponse || ''}</p>
                </div>
                ${etapes ? `<div style="margin-bottom:.75rem;"><p style="font-size:.75rem; font-weight:700; color:#889ABF; text-transform:uppercase; margin:0 0 .4rem;">Étapes</p><ol style="margin:0; padding-left:1.25rem;">${etapes}</ol></div>` : ''}
                <div style="display:flex; gap:.5rem;">
                    <div style="flex:1; padding:.5rem .75rem; background:#F9FAFB; border-radius:8px; text-align:center;">
                        <p style="font-size:.7rem; color:#9CA3AF; margin:0 0 .15rem; text-transform:uppercase;">Temps estimé</p>
                        <p style="font-size:.82rem; font-weight:700; color:#1A1F2E; margin:0;">${data.temps_estime || '—'}</p>
                    </div>
                    <div style="flex:1; padding:.5rem .75rem; background:${data.necessite_intervention ? '#FEF2F2' : '#F0FDF4'}; border-radius:8px; text-align:center;">
                        <p style="font-size:.7rem; color:#9CA3AF; margin:0 0 .15rem; text-transform:uppercase;">Intervention</p>
                        <p style="font-size:.82rem; font-weight:700; color:${data.necessite_intervention ? '#DC2626' : '#16A34A'}; margin:0;">${data.necessite_intervention ? 'Requise' : 'Non requise'}</p>
                    </div>
                </div>
                <button onclick="copierReponse()" style="width:100%; margin-top:.75rem; padding:.6rem; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:8px; font-size:.82rem; font-weight:600; cursor:pointer; font-family:inherit; color:#374151;">
                    Copier la réponse
                </button>
            `;
            window._derniereReponse = data.reponse;
        }
        document.getElementById('reponse-result').style.display = 'block';
    } catch (e) {
        document.getElementById('reponse-loading').style.display = 'none';
        showError('reponse-result-content', 'Erreur de connexion.');
        document.getElementById('reponse-result').style.display = 'block';
    }
}

function copierReponse() {
    navigator.clipboard.writeText(window._derniereReponse || '');
    alert('Réponse copiée dans le presse-papiers !');
}

// ─── Analyser KPI ────────────────────────────────────────────────────────────
async function analyserKPI() {
    document.getElementById('kpi-result').style.display = 'none';
    document.getElementById('kpi-loading').style.display = 'block';

    try {
        const res = await fetch('{{ route("ai.analyser-performances") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({})
        });
        const data = await res.json();
        document.getElementById('kpi-loading').style.display = 'none';

        if (data.erreur) {
            document.getElementById('kpi-result').innerHTML = `<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:1rem; color:#DC2626;">${data.erreur}</div>`;
        } else {
            const tendanceColor = data.tendance === 'positive' ? '#10B981' : (data.tendance === 'negative' ? '#EF4444' : '#F59E0B');
            const noteColor = data.note_globale >= 8 ? '#10B981' : (data.note_globale >= 5 ? '#F59E0B' : '#EF4444');

            const recs = (data.recommandations || []).map(r => {
                const pc = r.priorite === 'haute' ? '#EF4444' : (r.priorite === 'moyenne' ? '#F59E0B' : '#889ABF');
                return `<div style="padding:.75rem; border-radius:8px; border:1px solid #E5E7EB; margin-bottom:.5rem; border-left:3px solid ${pc};">
                    <p style="font-size:.82rem; font-weight:700; color:#1A1F2E; margin:0 0 .25rem;">${r.titre}</p>
                    <p style="font-size:.78rem; color:#6B7280; margin:0 0 .25rem;">${r.description}</p>
                    <p style="font-size:.72rem; color:${pc}; font-weight:600; margin:0;">Impact : ${r.impact}</p>
                </div>`;
            }).join('');

            document.getElementById('kpi-result').innerHTML = `
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.25rem; margin-bottom:1.5rem;">
                    <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center; border-top:4px solid ${noteColor};">
                        <p style="font-size:2.5rem; font-weight:900; color:${noteColor}; margin:0;">${data.note_globale}<span style="font-size:1rem;">/10</span></p>
                        <p style="font-size:.78rem; color:#9CA3AF; text-transform:uppercase; font-weight:600; margin:.3rem 0 0;">Note globale</p>
                    </div>
                    <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center; border-top:4px solid ${tendanceColor};">
                        <p style="font-size:1.8rem; font-weight:900; color:${tendanceColor}; margin:0;">${data.tendance === 'positive' ? '↑' : (data.tendance === 'negative' ? '↓' : '→')}</p>
                        <p style="font-size:.78rem; color:#9CA3AF; text-transform:uppercase; font-weight:600; margin:.3rem 0 0;">Tendance ${data.tendance}</p>
                    </div>
                    <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center; border-top:4px solid #889ABF;">
                        <p style="font-size:1.4rem; font-weight:900; color:#889ABF; margin:0;">${(data.recommandations || []).length}</p>
                        <p style="font-size:.78rem; color:#9CA3AF; text-transform:uppercase; font-weight:600; margin:.3rem 0 0;">Recommandations</p>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem;">
                    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                        <h4 style="font-size:.78rem; font-weight:700; color:#10B981; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">✓ Points forts</h4>
                        ${(data.points_forts || []).map(p => `<p style="font-size:.82rem; color:#374151; margin:0 0 .4rem; display:flex; gap:.4rem;"><span style="color:#10B981; flex-shrink:0;">•</span>${p}</p>`).join('')}
                    </div>
                    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                        <h4 style="font-size:.78rem; font-weight:700; color:#F59E0B; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">⚡ Axes d'amélioration</h4>
                        ${(data.points_amelioration || []).map(p => `<p style="font-size:.82rem; color:#374151; margin:0 0 .4rem; display:flex; gap:.4rem;"><span style="color:#F59E0B; flex-shrink:0;">•</span>${p}</p>`).join('')}
                    </div>
                </div>
                ${data.alertes?.length ? `<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:1rem; margin-bottom:1.25rem;"><h4 style="font-size:.78rem; font-weight:700; color:#DC2626; margin:0 0 .5rem; text-transform:uppercase;">⚠ Alertes</h4>${data.alertes.map(a => `<p style="font-size:.82rem; color:#DC2626; margin:0 0 .3rem;">• ${a}</p>`).join('')}</div>` : ''}
                <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.25rem;">
                    <h4 style="font-size:.78rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .75rem;">Recommandations</h4>
                    ${recs}
                </div>
                <div style="background:#F0F4FF; border-radius:12px; padding:1.25rem; border:1px solid #C7D2FE;">
                    <h4 style="font-size:.78rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .5rem;">Synthèse exécutive</h4>
                    <p style="font-size:.85rem; color:#374151; line-height:1.7; margin:0;">${data.resume_executif || ''}</p>
                </div>
            `;
        }
        document.getElementById('kpi-result').style.display = 'block';
    } catch (e) {
        document.getElementById('kpi-loading').style.display = 'none';
        document.getElementById('kpi-result').innerHTML = `<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:1rem; color:#DC2626;">Erreur de connexion.</div>`;
        document.getElementById('kpi-result').style.display = 'block';
    }
}

function showError(id, msg) {
    document.getElementById(id).innerHTML = `<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:8px; padding:.75rem; color:#DC2626; font-size:.82rem;">${msg}</div>`;
}
</script>
@endsection
