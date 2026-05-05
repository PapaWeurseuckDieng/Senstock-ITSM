@extends('layouts.app')

@section('title', 'Parc Informatique')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    
    <!-- Titre à gauche -->
    <div>
        <h1 class="page-title">Parc Informatique</h1>
        <p class="page-subtitle">
            Inventaire et suivi des actifs IT — {{ now()->format('d/m/Y') }}
        </p>
    </div>

    <!-- Boutons à droite -->
    @if(auth()->user()->isITStaff())
    <div style="display:flex; gap:.75rem; align-items:center;">
        
        <a href="{{ route('parc.export') }}"
           style="display:inline-flex; align-items:center; gap:.5rem; padding:.65rem 1.2rem; border:1px solid #889ABF; color:#889ABF; border-radius:8px; font-size:.88rem; font-weight:600; text-decoration:none; background:#fff;">
            CSV
        </a>

        <a href="{{ route('parc.export.pdf') }}"
           style="display:inline-flex; align-items:center; gap:.5rem; padding:.65rem 1.2rem; border:1px solid #EF4444; color:#EF4444; border-radius:8px; font-size:.88rem; font-weight:600; text-decoration:none; background:#fff;">
            PDF
        </a>

        <a href="{{ route('parc.create') }}"
           style="display:inline-flex; align-items:center; gap:.5rem; padding:.65rem 1.4rem; background:#889ABF; color:#fff; border-radius:8px; font-size:.88rem; font-weight:600; text-decoration:none;">
            Ajouter un équipement
        </a>

    </div>
    @endif
</div>

@if(session('success'))
<div style="background:#D1FAE5; border:1px solid #6EE7B7; border-radius:10px; padding:.9rem 1.25rem; margin-bottom:1.5rem; color:#065F46; font-size:.88rem; font-weight:500;">
    ✓ {{ session('success') }}
</div>
@endif

{{-- KPI Cards --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1.25rem; margin-bottom:2rem;">
    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #889ABF; text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#889ABF; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">Total équipements</p>
        <p style="font-size:2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $kpi['total'] }}</p>
    </div>
    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #10B981; text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#10B981; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">Actifs</p>
        <p style="font-size:2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $kpi['actifs'] }}</p>
    </div>
    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #F59E0B; text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#F59E0B; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">En maintenance</p>
        <p style="font-size:2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $kpi['en_maintenance'] }}</p>
    </div>
    <div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); border-left:4px solid #EF4444; text-align:center;">
        <p style="font-size:.72rem; font-weight:600; color:#EF4444; text-transform:uppercase; letter-spacing:.06em; margin:0 0 .4rem;">Garantie expirée</p>
        <p style="font-size:2rem; font-weight:800; color:#1A1F2E; margin:0;">{{ $kpi['garantie_expire'] }}</p>
    </div>
</div>

{{-- Répartition par catégorie --}}
@if($parCategorie->count())
<div style="background:#fff; border-radius:12px; padding:1.25rem 1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.5rem; display:flex; gap:1rem; flex-wrap:wrap; align-items:center;">
    <span style="font-size:.78rem; font-weight:700; color:#6B7280; text-transform:uppercase; letter-spacing:.05em;">Catégories :</span>
    @php
        $catColors = ['ordinateur_bureau'=>'#889ABF','ordinateur_portable'=>'#8B5CF6','serveur'=>'#EF4444','imprimante'=>'#F59E0B','switch'=>'#10B981','routeur'=>'#06B6D4','onduleur'=>'#F97316','iot'=>'#EC4899','telephone_ip'=>'#14B8A6','autre'=>'#9CA3AF'];
    @endphp
    @foreach($parCategorie as $cat => $total)
    <span style="display:inline-flex; align-items:center; gap:.4rem; background:rgba(0,0,0,.04); padding:.3rem .75rem; border-radius:20px; font-size:.78rem; font-weight:600; color:#374151;">
        <span style="width:8px; height:8px; border-radius:50%; background:{{ $catColors[$cat] ?? '#9CA3AF' }}; display:inline-block;"></span>
        {{ \App\Models\Equipement::CATEGORIES[$cat] ?? $cat }} ({{ $total }})
    </span>
    @endforeach
</div>
@endif

{{-- Filtres --}}
<div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('parc.index') }}" style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:.75rem; align-items:end;">
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase;">Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, code, série, IP…"
                style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; outline:none; box-sizing:border-box; font-family:inherit;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
        </div>
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase;">Catégorie</label>
            <select name="categorie" style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; background:#fff; font-family:inherit; cursor:pointer; outline:none;">
                <option value="">Toutes</option>
                @foreach(\App\Models\Equipement::CATEGORIES as $key => $label)
                    <option value="{{ $key }}" {{ request('categorie') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase;">Statut</label>
            <select name="statut" style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; background:#fff; font-family:inherit; cursor:pointer; outline:none;">
                <option value="">Tous</option>
                @foreach(\App\Models\Equipement::STATUTS as $key => $label)
                    <option value="{{ $key }}" {{ request('statut') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase;">Département</label>
            <input type="text" name="departement" value="{{ request('departement') }}" placeholder="Ex: Comptabilité"
                style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; outline:none; box-sizing:border-box; font-family:inherit;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
        </div>
        <div style="display:flex; gap:.5rem;">
            <button type="submit" style="padding:.6rem 1.2rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.85rem; font-weight:600; cursor:pointer; font-family:inherit; white-space:nowrap;">Filtrer</button>
            @if(request()->hasAny(['search','categorie','statut','departement']))
            <a href="{{ route('parc.index') }}" style="padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; color:#6B7280; text-decoration:none; background:#fff; display:flex; align-items:center;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Tableau --}}
<div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden;">
    <div style="padding:1rem 1.5rem; border-bottom:1px solid #F3F4F6;">
        <p style="font-size:.88rem; font-weight:700; color:#1A1F2E; margin:0;">
            {{ $equipements->total() }} équipement(s)
            @if(request()->hasAny(['search','categorie','statut','departement']))
                <span style="font-size:.78rem; color:#9CA3AF; font-weight:400;">— filtrés</span>
            @endif
        </p>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:.82rem;">
            <thead>
                <tr style="background:#F9FAFB; border-bottom:1px solid #F3F4F6;">
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Code</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Équipement</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Catégorie</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Affecté à</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Localisation</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Garantie</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Statut</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipements as $eq)
                <tr style="border-bottom:1px solid #F9FAFB;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background=''">
                    <td style="padding:.75rem 1rem;">
                        <span style="font-family:'DM Mono', monospace; font-size:.76rem; color:#889ABF; font-weight:700;">{{ $eq->code_inventaire }}</span>
                    </td>
                    <td style="padding:.75rem 1rem;">
                        <div style="display:flex; align-items:center; gap:.6rem;">
                            <span style="font-size:1.2rem;">{{ $eq->categorie_icon }}</span>
                            <div>
                                <p style="font-size:.85rem; font-weight:600; color:#1A1F2E; margin:0;">{{ $eq->nom }}</p>
                                @if($eq->marque || $eq->modele)
                                <p style="font-size:.75rem; color:#9CA3AF; margin:.1rem 0 0;">{{ trim($eq->marque . ' ' . $eq->modele) }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:.75rem 1rem;">
                        <span style="font-size:.78rem; color:#6B7280;">{{ $eq->category_label }}</span>
                    </td>
                    <td style="padding:.75rem 1rem;">
                        @if($eq->assignedUser)
                            <div style="display:flex; align-items:center; gap:.4rem;">
                                <div style="width:24px; height:24px; background:rgba(136,154,191,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:700; color:#889ABF; flex-shrink:0;">
                                    {{ strtoupper(substr($eq->assignedUser->name, 0, 1)) }}
                                </div>
                                <span style="font-size:.8rem; color:#374151;">{{ $eq->assignedUser->name }}</span>
                            </div>
                        @else
                            <span style="font-size:.78rem; color:#9CA3AF;">—</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem; font-size:.8rem; color:#6B7280;">{{ $eq->localisation ?? '—' }}</td>
                    <td style="padding:.75rem 1rem;">
                        @if($eq->fin_garantie)
                            @if($eq->garantie_expireed)
                                <span style="font-size:.75rem; font-weight:600; color:#EF4444;">
                                    Expirée {{ $eq->fin_garantie->format('d/m/Y') }}
                                </span>
                            @elseif($eq->garantie_expiration_soon)
                                <span style="font-size:.75rem; font-weight:600; color:#F59E0B;">
                                    ⚠ {{ $eq->fin_garantie->format('d/m/Y') }}
                                </span>
                            @else
                                <span style="font-size:.75rem; color:#10B981;">{{ $eq->fin_garantie->format('d/m/Y') }}</span>
                            @endif
                        @else
                            <span style="font-size:.75rem; color:#9CA3AF;">—</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem;">
                        @php $color = $eq->statut_color; @endphp
                        <span style="font-size:.72rem; font-weight:600; padding:.25rem .65rem; border-radius:20px; background:{{ $color['bg'] }}; color:{{ $color['text'] }}; white-space:nowrap;">
                            {{ $eq->statut_label }}
                        </span>
                    </td>
                    <td style="padding:.75rem 1rem;">
                        <div style="display:flex; gap:.4rem;">
                            <a href="{{ route('parc.show', $eq) }}" style="font-size:.78rem; color:#889ABF; font-weight:600; text-decoration:none; background:rgba(136,154,191,.1); padding:.3rem .75rem; border-radius:6px; white-space:nowrap;">Détail</a>
                            @if(auth()->user()->isITStaff())
                            <a href="{{ route('parc.edit', $eq) }}" style="font-size:.78rem; color:#6B7280; font-weight:600; text-decoration:none; background:#F3F4F6; padding:.3rem .75rem; border-radius:6px; white-space:nowrap;">Modifier</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding:4rem; color:#9CA3AF;">
                        <div style="font-size:2.5rem; margin-bottom:.75rem;">🖥️</div>
                        <p style="margin:0; font-size:.9rem; font-weight:600; color:#6B7280;">Aucun équipement trouvé</p>
                        <p style="margin:.25rem 0 1rem; font-size:.8rem;">Commencez par ajouter un équipement au parc.</p>
                        <a href="{{ route('parc.create') }}" style="display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1.2rem; background:#889ABF; color:#fff; border-radius:8px; font-size:.82rem; font-weight:600; text-decoration:none;">
                            + Ajouter un équipement
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($equipements->hasPages())
    <div style="padding:1rem 1.5rem; border-top:1px solid #F3F4F6;">
        {{ $equipements->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
