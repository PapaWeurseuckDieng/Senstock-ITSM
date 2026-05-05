@extends('layouts.app')

@section('title', 'Base de connaissances IT')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Base de connaissances IT</h1>
        <p class="page-subtitle">Solutions et guides techniques propulsés par l'IA</p>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 320px; gap:1.5rem;">

    {{-- Recherche principale --}}
    <div>
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.5rem;">
            <h3 style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem;">Rechercher une solution</h3>
            <div style="display:flex; gap:.75rem;">
                <input type="text" id="kb-question" placeholder="Ex: Impossible de se connecter au réseau WiFi, imprimante hors ligne…"
                    style="flex:1; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; font-family:inherit;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'"
                    onkeydown="if(event.key==='Enter') rechercherSolution()">
                <button onclick="rechercherSolution()"
                    style="padding:.65rem 1.4rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.88rem; font-weight:700; cursor:pointer; font-family:inherit; white-space:nowrap;">
                    Rechercher
                </button>
            </div>
        </div>

        {{-- Résultat --}}
        <div id="kb-loading" style="display:none; background:#fff; border-radius:12px; padding:3rem; box-shadow:0 2px 8px rgba(0,0,0,.06); text-align:center;">
            <div style="width:40px; height:40px; border:3px solid #E5E7EB; border-top-color:#889ABF; border-radius:50%; animation:spin 1s linear infinite; margin:0 auto 1rem;"></div>
            <p style="font-size:.88rem; color:#6B7280;">Recherche en cours…</p>
        </div>

        <div id="kb-result" style="display:none; background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);"></div>

        {{-- Solutions populaires --}}
        <div id="kb-populaires">
            <h3 style="font-size:.85rem; font-weight:700; color:#1A1F2E; margin:0 0 1rem;">Solutions fréquentes</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:.75rem;">
                @foreach([
                    ['titre'=>'Connexion WiFi impossible',       'cat'=>'Réseau',    'icon'=>'📡'],
                    ['titre'=>'Outlook ne se lance pas',         'cat'=>'Logiciel',  'icon'=>'📧'],
                    ['titre'=>'Imprimante hors ligne',           'cat'=>'Matériel',  'icon'=>'🖨️'],
                    ['titre'=>'Mot de passe oublié / bloqué',    'cat'=>'Accès',     'icon'=>'🔐'],
                    ['titre'=>'Ordinateur lent au démarrage',    'cat'=>'Matériel',  'icon'=>'💻'],
                    ['titre'=>'Partage de fichiers réseau',      'cat'=>'Réseau',    'icon'=>'📁'],
                    ['titre'=>'Écran bleu (BSOD) Windows',      'cat'=>'Système',   'icon'=>'🖥️'],
                    ['titre'=>'VPN connexion échoue',            'cat'=>'Réseau',    'icon'=>'🔒'],
                ] as $sol)
                <button onclick="rechercherAvec('{{ $sol['titre'] }}')"
                    style="display:flex; align-items:center; gap:.75rem; padding:.85rem 1rem; background:#fff; border:1px solid #E5E7EB; border-radius:10px; cursor:pointer; font-family:inherit; text-align:left; transition:all .15s;"
                    onmouseover="this.style.borderColor='#889ABF';this.style.boxShadow='0 2px 8px rgba(136,154,191,.2)'"
                    onmouseout="this.style.borderColor='#E5E7EB';this.style.boxShadow='none'">
                    <span style="font-size:1.4rem; flex-shrink:0;">{{ $sol['icon'] }}</span>
                    <div>
                        <p style="font-size:.82rem; font-weight:600; color:#1A1F2E; margin:0;">{{ $sol['titre'] }}</p>
                        <p style="font-size:.72rem; color:#889ABF; margin:.1rem 0 0; font-weight:600;">{{ $sol['cat'] }}</p>
                    </div>
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sidebar catégories --}}
    <div style="display:flex; flex-direction:column; gap:1rem;">
        <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .75rem;">Catégories</h3>
            @foreach([
                ['Réseau & Connectivité',   '📡', 'reseau wifi ethernet connexion'],
                ['Matériel & Équipements',  '🖥️', 'ordinateur imprimante peripherique'],
                ['Logiciels & Applications','📦', 'installation mise a jour application'],
                ['Sécurité & Accès',        '🔐', 'mot de passe acces permission compte'],
                ['Messagerie & Outlook',    '📧', 'email outlook messagerie office365'],
                ['Serveurs & Infrastructure','🖧', 'serveur réseau infrastructure stockage'],
            ] as [$cat, $icon, $mots])
            <button onclick="rechercherAvec('{{ $mots }}')"
                style="width:100%; display:flex; align-items:center; gap:.6rem; padding:.6rem .75rem; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:8px; cursor:pointer; font-family:inherit; text-align:left; margin-bottom:.4rem; font-size:.8rem; font-weight:600; color:#374151;"
                onmouseover="this.style.background='#F0F4FF';this.style.borderColor='#889ABF'"
                onmouseout="this.style.background='#F9FAFB';this.style.borderColor='#E5E7EB'">
                <span>{{ $icon }}</span> {{ $cat }}
            </button>
            @endforeach
        </div>

        <div style="background:linear-gradient(135deg,#889ABF,#5B6FA8); border-radius:12px; padding:1.25rem; color:#fff;">
            <p style="font-size:.82rem; font-weight:700; margin:0 0 .4rem;">Solution non trouvée ?</p>
            <p style="font-size:.75rem; opacity:.85; margin:0 0 .75rem; line-height:1.5;">Créez un ticket et notre équipe IT vous assistera rapidement.</p>
            <a href="{{ route('tickets.create') }}"
                style="display:block; text-align:center; padding:.5rem; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.4); border-radius:8px; font-size:.82rem; font-weight:700; color:#fff; text-decoration:none;">
                Créer un ticket →
            </a>
        </div>
    </div>
</div>

<style>
@keyframes spin { to { transform:rotate(360deg); } }
</style>

<script>
const CSRF = '{{ csrf_token() }}';

function rechercherAvec(q) {
    document.getElementById('kb-question').value = q;
    rechercherSolution();
}

async function rechercherSolution() {
    const q = document.getElementById('kb-question').value.trim();
    if (!q) return;

    document.getElementById('kb-populaires').style.display = 'none';
    document.getElementById('kb-result').style.display = 'none';
    document.getElementById('kb-loading').style.display = 'block';

    try {
        const res = await fetch('{{ route("ai.rechercher-solution") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ question: q })
        });
        const data = await res.json();
        document.getElementById('kb-loading').style.display = 'none';

        if (data.erreur) {
            document.getElementById('kb-result').innerHTML = `<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:8px; padding:1rem; color:#DC2626;">${data.erreur}</div>`;
        } else {
            const diffColor = data.difficulte === 'facile' ? '#10B981' : (data.difficulte === 'expert' ? '#EF4444' : '#F59E0B');
            const etapes = (data.etapes || []).map((e, i) =>
                `<div style="display:flex; gap:.75rem; align-items:flex-start; padding:.6rem 0; border-bottom:1px solid #F9FAFB;">
                    <span style="width:24px; height:24px; background:#889ABF; color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:800; flex-shrink:0;">${i+1}</span>
                    <p style="font-size:.85rem; color:#374151; margin:0; line-height:1.5;">${e}</p>
                </div>`
            ).join('');

            const outils = (data.outils_necessaires || []).map(o =>
                `<span style="display:inline-block; background:#F0F4FF; border:1px solid #C7D2FE; color:#5B6FA8; padding:.25rem .65rem; border-radius:20px; font-size:.75rem; font-weight:600; margin:.2rem;">${o}</span>`
            ).join('');

            document.getElementById('kb-result').innerHTML = `
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem; padding-bottom:1rem; border-bottom:1px solid #F3F4F6;">
                    <h3 style="font-size:.9rem; font-weight:700; color:#1A1F2E; margin:0;">Solution trouvée</h3>
                    <div style="display:flex; gap:.5rem; align-items:center;">
                        <span style="font-size:.72rem; font-weight:700; color:${diffColor}; background:${diffColor}20; padding:.2rem .65rem; border-radius:20px;">${data.difficulte}</span>
                        <span style="font-size:.72rem; color:#9CA3AF;">${data.temps_estime}</span>
                    </div>
                </div>
                <div style="padding:.75rem; background:#F0F4FF; border-radius:8px; border-left:3px solid #889ABF; margin-bottom:1.25rem;">
                    <p style="font-size:.88rem; color:#374151; line-height:1.7; margin:0;">${data.solution}</p>
                </div>
                ${etapes ? `<h4 style="font-size:.78rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .5rem;">Étapes</h4>${etapes}<div style="margin-bottom:1.25rem;"></div>` : ''}
                ${outils ? `<div style="margin-bottom:1rem;"><h4 style="font-size:.78rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .4rem;">Outils nécessaires</h4>${outils}</div>` : ''}
                ${data.prevenir_recurrence ? `<div style="padding:.75rem; background:#F0FDF4; border-radius:8px; border:1px solid #86EFAC;"><h4 style="font-size:.72rem; font-weight:700; color:#065F46; text-transform:uppercase; margin:0 0 .3rem;">Prévention</h4><p style="font-size:.82rem; color:#065F46; margin:0;">${data.prevenir_recurrence}</p></div>` : ''}
                <button onclick="document.getElementById('kb-populaires').style.display='block'; document.getElementById('kb-result').style.display='none';"
                    style="margin-top:1rem; padding:.5rem 1rem; background:#F9FAFB; border:1px solid #E5E7EB; border-radius:8px; font-size:.8rem; cursor:pointer; font-family:inherit; color:#6B7280;">
                    ← Retour aux solutions fréquentes
                </button>
            `;
        }
        document.getElementById('kb-result').style.display = 'block';
    } catch (e) {
        document.getElementById('kb-loading').style.display = 'none';
        document.getElementById('kb-result').innerHTML = `<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:8px; padding:1rem; color:#DC2626;">Erreur de connexion à l'IA.</div>`;
        document.getElementById('kb-result').style.display = 'block';
    }
}
</script>
@endsection
