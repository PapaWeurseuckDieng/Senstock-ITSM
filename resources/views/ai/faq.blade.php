@extends('layouts.app')

@section('title', 'FAQ Intelligente')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">FAQ Intelligente</h1>
        <p class="page-subtitle">Questions fréquentes avec réponses générées par l'IA</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 300px; gap:1.5rem;">

    <div>
        {{-- Saisie question --}}
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.5rem;">
            <div style="display:flex; gap:.75rem; align-items:center;">
                <div style="width:40px; height:40px; background:linear-gradient(135deg,#889ABF,#5B6FA8); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3M12 17h.01"/></svg>
                </div>
                <input type="text" id="faq-question" placeholder="Posez votre question…"
                    style="flex:1; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; font-family:inherit;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'"
                    onkeydown="if(event.key==='Enter') poserQuestion()">
                <button onclick="poserQuestion()"
                    style="padding:.65rem 1.2rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.88rem; font-weight:700; cursor:pointer; font-family:inherit;">
                    Demander
                </button>
            </div>
        </div>

        {{-- Résultat --}}
        <div id="faq-loading" style="display:none; background:#fff; border-radius:12px; padding:2.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center; margin-bottom:1.5rem;">
            <div style="width:36px; height:36px; border:3px solid #E5E7EB; border-top-color:#889ABF; border-radius:50%; animation:spin 1s linear infinite; margin:0 auto .75rem;"></div>
            <p style="font-size:.85rem; color:#6B7280; margin:0;">Recherche de la réponse…</p>
        </div>

        <div id="faq-result" style="display:none; background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.5rem;"></div>

        {{-- FAQ statique --}}
        <div id="faq-statique">
            <h3 style="font-size:.85rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem;">Questions fréquentes</h3>
            @foreach([
                ['q'=>'Comment créer un ticket ?',                  'r'=>'Cliquez sur "Nouveau ticket" dans le menu Tickets, remplissez le formulaire avec le titre, la description et la priorité, puis validez. Vous recevrez une confirmation par email.'],
                ['q'=>'Quel est le délai de traitement SLA ?',       'r'=>'Critique : 4h — Haute : 8h — Normale : 24h — Faible : 72h. Ces délais comptent à partir de la création du ticket.'],
                ['q'=>'Comment suivre l\'avancement de mon ticket ?','r'=>'Connectez-vous et allez dans "Mes tickets". Vous verrez le statut en temps réel. Vous recevez aussi des notifications email à chaque changement.'],
                ['q'=>'Que faire si mon problème est urgent ?',      'r'=>'Créez un ticket avec la priorité "Critique" ou "Haute". Pour une urgence extrême, appelez directement l\'équipe IT au +221 77 000 00 02.'],
                ['q'=>'Comment signaler une panne réseau ?',         'r'=>'Créez un ticket de type "Panne" avec la priorité selon l\'impact. Précisez combien de personnes sont affectées et depuis quand.'],
                ['q'=>'Puis-je fermer moi-même un ticket ?',        'r'=>'Oui, quand le technicien résout votre ticket, vous recevez un lien pour valider la résolution et attribuer une note de satisfaction.'],
            ] as $faq)
            <div style="border:1px solid #E5E7EB; border-radius:10px; margin-bottom:.6rem; overflow:hidden;">
                <button onclick="toggleFaq(this)"
                    style="width:100%; display:flex; justify-content:space-between; align-items:center; padding:.9rem 1.1rem; background:#fff; border:none; cursor:pointer; font-family:inherit; font-size:.85rem; font-weight:600; color:#1A1F2E; text-align:left;"
                    onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                    {{ $faq['q'] }}
                    <svg class="faq-arrow" width="16" height="16" fill="none" stroke="#9CA3AF" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0; transition:transform .2s;"><path d="M6 9l6 6 6-6"/></svg>
                </button>
                <div class="faq-body" style="display:none; padding:.75rem 1.1rem 1rem; border-top:1px solid #F3F4F6;">
                    <p style="font-size:.84rem; color:#6B7280; margin:0; line-height:1.7;">{{ $faq['r'] }}</p>
                    <button onclick="posAvecQuestion('{{ $faq['q'] }}')"
                        style="margin-top:.6rem; padding:.3rem .75rem; background:#F0F4FF; border:1px solid #C7D2FE; border-radius:6px; font-size:.75rem; color:#5B6FA8; cursor:pointer; font-family:inherit; font-weight:600;">
                        En savoir plus avec l'IA →
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Sidebar --}}
    <div style="display:flex; flex-direction:column; gap:1rem;">
        <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .75rem;">Raccourcis</h3>
            @foreach([
                ['label'=>'Créer un ticket',        'route'=>'tickets.create',          'color'=>'#889ABF'],
                ['label'=>'Mes tickets',             'route'=>'tickets.index',           'color'=>'#6B7280'],
                ['label'=>'Base de connaissances',   'route'=>'ai.base-connaissances',   'color'=>'#8B5CF6'],
                ['label'=>'Assistant IA',            'route'=>'ai.assistant',            'color'=>'#5B6FA8'],
            ] as $lien)
            <a href="{{ route($lien['route']) }}"
                style="display:flex; align-items:center; gap:.5rem; padding:.5rem .75rem; border-radius:8px; font-size:.82rem; font-weight:600; color:{{ $lien['color'] }}; text-decoration:none; margin-bottom:.35rem; background:{{ $lien['color'] }}10; border:1px solid {{ $lien['color'] }}30;">
                → {{ $lien['label'] }}
            </a>
            @endforeach
        </div>

        <div style="background:#F0F4FF; border-radius:12px; padding:1.25rem; border:1px solid #C7D2FE;">
            <p style="font-size:.8rem; font-weight:700; color:#5B6FA8; margin:0 0 .4rem;">💡 Astuce</p>
            <p style="font-size:.75rem; color:#6B7280; margin:0; line-height:1.5;">
                Posez des questions précises pour obtenir les meilleures réponses de l'IA. 
                Mentionnez le logiciel, l'équipement ou le symptôme exact.
            </p>
        </div>
    </div>
</div>

<style>
@keyframes spin { to { transform:rotate(360deg); } }
</style>

<script>
const CSRF = '{{ csrf_token() }}';

function toggleFaq(btn) {
    const body = btn.nextElementSibling;
    const arrow = btn.querySelector('.faq-arrow');
    const open = body.style.display === 'block';
    body.style.display = open ? 'none' : 'block';
    arrow.style.transform = open ? '' : 'rotate(180deg)';
}

function posAvecQuestion(q) {
    document.getElementById('faq-question').value = q;
    poserQuestion();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function posErQuestion(q) {
    document.getElementById('faq-question').value = q;
    poserQuestion();
}

async function poserQuestion() {
    const q = document.getElementById('faq-question').value.trim();
    if (!q) return;

    document.getElementById('faq-result').style.display = 'none';
    document.getElementById('faq-loading').style.display = 'block';

    try {
        const res = await fetch('{{ route("ai.faq.repondre") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ question: q })
        });
        const data = await res.json();
        document.getElementById('faq-loading').style.display = 'none';

        if (data.erreur) {
            document.getElementById('faq-result').innerHTML = `<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:8px; padding:1rem; color:#DC2626;">${data.erreur}</div>`;
        } else {
            const urgColor = data.urgence === 'haute' ? '#EF4444' : (data.urgence === 'normale' ? '#F59E0B' : '#10B981');
            const actions = (data.actions || []).map(a =>
                `<div style="display:flex; gap:.5rem; align-items:flex-start; padding:.4rem 0; font-size:.82rem; color:#374151;"><span style="color:#889ABF; flex-shrink:0;">→</span>${a}</div>`
            ).join('');

            document.getElementById('faq-result').innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid #F3F4F6;">
                    <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0;">Réponse IA</h3>
                    <span style="font-size:.72rem; font-weight:700; color:${urgColor}; background:${urgColor}20; padding:.2rem .65rem; border-radius:20px;">Urgence ${data.urgence}</span>
                </div>
                <div style="padding:.75rem; background:#F0F4FF; border-radius:8px; border-left:3px solid #889ABF; margin-bottom:1rem;">
                    <p style="font-size:.88rem; color:#374151; line-height:1.7; margin:0;">${data.reponse}</p>
                </div>
                ${actions ? `<div style="margin-bottom:1rem;"><h4 style="font-size:.75rem; font-weight:700; color:#889ABF; text-transform:uppercase; margin:0 0 .4rem;">Actions possibles</h4>${actions}</div>` : ''}
                ${data.creer_ticket ? `
                <div style="background:#FFF7ED; border:1px solid #FED7AA; border-radius:8px; padding:.75rem; display:flex; justify-content:space-between; align-items:center;">
                    <p style="font-size:.82rem; color:#92400E; margin:0; font-weight:600;">Ce problème nécessite peut-être un ticket</p>
                    <a href="{{ route('tickets.create') }}" style="padding:.35rem .9rem; background:#F59E0B; color:#fff; border-radius:6px; font-size:.78rem; font-weight:700; text-decoration:none; white-space:nowrap;">Créer →</a>
                </div>` : ''}
            `;
        }
        document.getElementById('faq-result').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } catch (e) {
        document.getElementById('faq-loading').style.display = 'none';
        document.getElementById('faq-result').innerHTML = `<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:8px; padding:1rem; color:#DC2626;">Erreur de connexion à l'IA.</div>`;
        document.getElementById('faq-result').style.display = 'block';
    }
}
</script>
@endsection
