@extends('layouts.app')

@section('title', $equipement->code_inventaire)

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:.6rem;">
            <span style="font-size:1.6rem;">{{ $equipement->categorie_icon }}</span>
            {{ $equipement->nom }}
        </h1>
        <p class="page-subtitle" style="font-family:'DM Mono',monospace; color:#889ABF;">{{ $equipement->code_inventaire }}</p>
    </div>
    <div style="display:flex; gap:.75rem;">
        @if(auth()->user()->isITStaff())
        <a href="{{ route('parc.edit', $equipement) }}"
            style="display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.2rem; background:#889ABF; color:#fff; border-radius:8px; font-size:.85rem; font-weight:600; text-decoration:none;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Modifier
        </a>
        @endif
        <a href="{{ route('parc.index') }}"
            style="display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.2rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; font-weight:600; color:#6B7280; text-decoration:none; background:#fff;">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Retour
        </a>
    </div>
</div>

@if(session('success'))
<div style="background:#D1FAE5; border:1px solid #6EE7B7; border-radius:10px; padding:.9rem 1.25rem; margin-bottom:1.5rem; color:#065F46; font-size:.88rem;">
    ✓ {{ session('success') }}
</div>
@endif

<div style="display:grid; grid-template-columns:2fr 1fr; gap:1.5rem;">

    {{-- Colonne principale --}}
    <div style="display:flex; flex-direction:column; gap:1.5rem;">

        {{-- Informations générales --}}
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Informations générales</h3>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                @php
                function infoRow($label, $value, $mono = false) {
                    $style = $mono ? "font-family:'DM Mono',monospace; font-size:.82rem;" : "font-size:.88rem;";
                    return "<div style='padding:.75rem; background:#F9FAFB; border-radius:8px;'>
                        <p style='font-size:.72rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .3rem;'>{$label}</p>
                        <p style='{$style} font-weight:600; color:#1A1F2E; margin:0;'>" . ($value ?: '<span style=\"color:#9CA3AF; font-weight:400;\">—</span>') . "</p>
                    </div>";
                }
                @endphp

                {!! infoRow('Catégorie', $equipement->category_label) !!}
                {!! infoRow('Marque / Modèle', trim($equipement->marque . ' ' . $equipement->modele)) !!}
                {!! infoRow('Numéro de série', $equipement->numero_serie, true) !!}
                {!! infoRow('Adresse MAC', $equipement->adresse_mac, true) !!}
                {!! infoRow('Adresse IP', $equipement->adresse_ip, true) !!}
                {!! infoRow('Localisation', $equipement->localisation) !!}
                {!! infoRow('Département', $equipement->departement) !!}

                @if($equipement->assignedUser)
                <div style="padding:.75rem; background:#F0F4FF; border-radius:8px; border:1px solid #C7D2FE;">
                    <p style="font-size:.72rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .3rem;">Affecté à</p>
                    <div style="display:flex; align-items:center; gap:.5rem;">
                        <div style="width:28px; height:28px; background:#889ABF; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; color:#fff;">
                            {{ strtoupper(substr($equipement->assignedUser->name, 0, 1)) }}
                        </div>
                        <div>
                            <p style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0;">{{ $equipement->assignedUser->name }}</p>
                            <p style="font-size:.72rem; color:#889ABF; margin:0;">{{ $equipement->assignedUser->role_label }}</p>
                        </div>
                    </div>
                </div>
                @else
                {!! infoRow('Affecté à', '') !!}
                @endif
            </div>
        </div>

        {{-- Spécifications techniques --}}
        @if($equipement->specifications)
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Spécifications techniques</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                @php
                $specLabels = ['cpu' => 'Processeur', 'ram' => 'Mémoire RAM', 'stockage' => 'Stockage', 'os' => 'Système d\'exploitation'];
                @endphp
                @foreach($equipement->specifications as $key => $val)
                <div style="padding:.75rem; background:#F9FAFB; border-radius:8px;">
                    <p style="font-size:.72rem; font-weight:600; color:#9CA3AF; text-transform:uppercase; letter-spacing:.05em; margin:0 0 .3rem;">{{ $specLabels[$key] ?? $key }}</p>
                    <p style="font-size:.88rem; font-weight:600; color:#1A1F2E; margin:0;">{{ $val }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Notes --}}
        @if($equipement->notes)
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1rem;">Notes</h3>
            <p style="font-size:.88rem; color:#374151; line-height:1.7; margin:0; white-space:pre-wrap;">{{ $equipement->notes }}</p>
        </div>
        @endif

        {{-- Historique des mouvements --}}
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">
                Historique des mouvements
                <span style="font-size:.75rem; font-weight:400; color:#9CA3AF; text-transform:none; letter-spacing:0; margin-left:.5rem;">{{ $equipement->historique->count() }} entrée(s)</span>
            </h3>

            @forelse($equipement->historique as $h)
            <div style="display:flex; gap:.75rem; padding:.75rem 0; border-bottom:1px solid #F9FAFB; align-items:flex-start;">
                <div style="width:32px; height:32px; background:rgba(136,154,191,.15); border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:.1rem;">
                    @php
                        $hicons = ['affectation'=>'👤','desaffectation'=>'↩','maintenance'=>'🔧','retour_stock'=>'📦','mise_hors_service'=>'❌','changement_localisation'=>'📍','autre'=>'📝'];
                    @endphp
                    <span style="font-size:.9rem;">{{ $hicons[$h->type_mouvement] ?? '📝' }}</span>
                </div>
                <div style="flex:1;">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <p style="font-size:.85rem; font-weight:600; color:#1A1F2E; margin:0;">{{ $h->type_label }}</p>
                        <span style="font-size:.72rem; color:#9CA3AF; font-family:'DM Mono',monospace; flex-shrink:0; margin-left:.5rem;">{{ $h->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($h->ancienne_valeur || $h->nouvelle_valeur)
                    <p style="font-size:.78rem; color:#6B7280; margin:.2rem 0 0;">
                        @if($h->ancienne_valeur)
                            <span style="background:#FEE2E2; color:#DC2626; padding:.1rem .4rem; border-radius:4px;">{{ $h->ancienne_valeur }}</span>
                        @endif
                        @if($h->ancienne_valeur && $h->nouvelle_valeur)
                            <span style="margin:0 .3rem; color:#9CA3AF;">→</span>
                        @endif
                        @if($h->nouvelle_valeur)
                            <span style="background:#D1FAE5; color:#065F46; padding:.1rem .4rem; border-radius:4px;">{{ $h->nouvelle_valeur }}</span>
                        @endif
                    </p>
                    @endif
                    @if($h->commentaire)
                    <p style="font-size:.78rem; color:#9CA3AF; margin:.2rem 0 0; font-style:italic;">{{ $h->commentaire }}</p>
                    @endif
                    @if($h->user)
                    <p style="font-size:.72rem; color:#9CA3AF; margin:.2rem 0 0;">par {{ $h->user->name }}</p>
                    @endif
                </div>
            </div>
            @empty
            <p style="text-align:center; color:#9CA3AF; font-size:.85rem; padding:1rem 0;">Aucun mouvement enregistré.</p>
            @endforelse
        </div>

        {{-- Tickets liés --}}
        @if($equipement->tickets->count())
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Tickets liés ({{ $equipement->tickets->count() }})</h3>
            @foreach($equipement->tickets as $ticket)
            <div style="display:flex; justify-content:space-between; align-items:center; padding:.65rem .75rem; background:#F9FAFB; border-radius:8px; margin-bottom:.5rem;">
                <span style="font-family:'DM Mono',monospace; font-size:.78rem; color:#889ABF; font-weight:700;">{{ $ticket->ticket_number }}</span>
                <span style="font-size:.82rem; color:#374151; flex:1; margin:0 1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $ticket->title }}</span>
                <a href="{{ route('tickets.show', $ticket) }}" style="font-size:.75rem; color:#889ABF; font-weight:600; text-decoration:none;">Voir →</a>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div style="display:flex; flex-direction:column; gap:1.5rem;">

        {{-- Statut --}}
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            @php $color = $equipement->statut_color; @endphp
            <div style="text-align:center; padding:1rem; background:{{ $color['bg'] }}; border-radius:10px; margin-bottom:1rem;">
                <p style="font-size:1.1rem; font-weight:800; color:{{ $color['text'] }}; margin:0;">{{ $equipement->statut_label }}</p>
            </div>
            <p style="font-size:.75rem; color:#9CA3AF; text-align:center; margin:0;">Ajouté le {{ $equipement->created_at->format('d/m/Y') }}</p>
        </div>

        {{-- Garantie --}}
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1rem;">Garantie</h3>

            @if($equipement->fin_garantie)
                @if($equipement->garantie_expireed)
                <div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:8px; padding:.75rem; text-align:center;">
                    <p style="font-size:.78rem; font-weight:700; color:#DC2626; margin:0;">⚠ Garantie expirée</p>
                    <p style="font-size:.82rem; color:#DC2626; margin:.3rem 0 0;">{{ $equipement->fin_garantie->format('d/m/Y') }}</p>
                    <p style="font-size:.72rem; color:#9CA3AF; margin:.2rem 0 0;">Il y a {{ $equipement->fin_garantie->diffForHumans() }}</p>
                </div>
                @elseif($equipement->garantie_expiration_soon)
                <div style="background:#FFFBEB; border:1px solid #FCD34D; border-radius:8px; padding:.75rem; text-align:center;">
                    <p style="font-size:.78rem; font-weight:700; color:#D97706; margin:0;">⚠ Expire bientôt</p>
                    <p style="font-size:.82rem; color:#D97706; margin:.3rem 0 0;">{{ $equipement->fin_garantie->format('d/m/Y') }}</p>
                    <p style="font-size:.72rem; color:#9CA3AF; margin:.2rem 0 0;">Dans {{ $equipement->fin_garantie->diffForHumans() }}</p>
                </div>
                @else
                <div style="background:#F0FDF4; border:1px solid #86EFAC; border-radius:8px; padding:.75rem; text-align:center;">
                    <p style="font-size:.78rem; font-weight:700; color:#16A34A; margin:0;">✓ Sous garantie</p>
                    <p style="font-size:.82rem; color:#16A34A; margin:.3rem 0 0;">{{ $equipement->fin_garantie->format('d/m/Y') }}</p>
                    <p style="font-size:.72rem; color:#9CA3AF; margin:.2rem 0 0;">Expire {{ $equipement->fin_garantie->diffForHumans() }}</p>
                </div>
                @endif
            @else
                <p style="font-size:.85rem; color:#9CA3AF; text-align:center;">Non renseignée</p>
            @endif
        </div>

        {{-- Achat --}}
        @if($equipement->date_achat || $equipement->prix_achat || $equipement->fournisseur)
        <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
            <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1rem;">Achat</h3>
            <div style="display:flex; flex-direction:column; gap:.6rem; font-size:.85rem; color:#374151;">
                @if($equipement->date_achat)
                <div style="display:flex; justify-content:space-between;"><span style="color:#9CA3AF;">Date</span><span style="font-weight:600;">{{ $equipement->date_achat->format('d/m/Y') }}</span></div>
                @endif
                @if($equipement->prix_achat)
                <div style="display:flex; justify-content:space-between;"><span style="color:#9CA3AF;">Prix</span><span style="font-weight:600;">{{ number_format($equipement->prix_achat, 0, ',', ' ') }} FCFA</span></div>
                @endif
                @if($equipement->fournisseur)
                <div style="display:flex; justify-content:space-between;"><span style="color:#9CA3AF;">Fournisseur</span><span style="font-weight:600;">{{ $equipement->fournisseur }}</span></div>
                @endif
                @if($equipement->numero_bon_commande)
                <div style="display:flex; justify-content:space-between;"><span style="color:#9CA3AF;">Bon commande</span><span style="font-weight:600; font-family:'DM Mono',monospace; font-size:.78rem;">{{ $equipement->numero_bon_commande }}</span></div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
