@extends('layouts.app')

@section('title', isset($equipement) ? 'Modifier l\'équipement' : 'Ajouter un équipement')

@section('content')
@php
    $isEdit = isset($equipement);
    $eq     = $equipement ?? null;
    $specs  = $eq?->specifications ?? [];
@endphp

<div class="page-header">
    <div>
        <h1 class="page-title">{{ $isEdit ? 'Modifier l\'équipement' : 'Ajouter un équipement' }}</h1>
        <p class="page-subtitle">{{ $isEdit ? $eq->code_inventaire . ' — ' . $eq->nom : 'Nouvel actif au parc informatique' }}</p>
    </div>
    <a href="{{ $isEdit ? route('parc.show', $eq) : route('parc.index') }}"
        style="display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.2rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; font-weight:600; color:#6B7280; text-decoration:none; background:#fff;">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Retour
    </a>
</div>

@if($errors->any())
<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.5rem;">
    <p style="font-weight:600; color:#DC2626; margin:0 0 .5rem;">Veuillez corriger les erreurs suivantes :</p>
    <ul style="margin:0; padding-left:1.25rem; color:#DC2626; font-size:.85rem;">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form id="form-equipement" method="POST" action="{{ $isEdit ? route('parc.update', $eq) : route('parc.store') }}">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div style="display:grid; grid-template-columns:2fr 1fr; gap:1.5rem;">

        {{-- Colonne principale --}}
        <div style="display:flex; flex-direction:column; gap:1.5rem;">

            {{-- Identification --}}
            <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Identification</h3>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">

                    <div style="grid-column:1/-1;">
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Nom / Désignation <span style="color:#EF4444;">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom', $eq?->nom ?? '') }}" required
                            placeholder="Ex: Dell Latitude 5420 — Comptabilité"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>

                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Catégorie <span style="color:#EF4444;">*</span></label>
                        <select name="categorie" required
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; background:#fff; font-family:inherit; cursor:pointer; outline:none;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                            @foreach(\App\Models\Equipement::CATEGORIES as $key => $label)
                                <option value="{{ $key }}" {{ old('categorie', $eq?->categorie ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Statut <span style="color:#EF4444;">*</span></label>
                        <select name="statut" required
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; background:#fff; font-family:inherit; cursor:pointer; outline:none;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                            @foreach(\App\Models\Equipement::STATUTS as $key => $label)
                                <option value="{{ $key }}" {{ old('statut', $eq?->statut ?? 'actif') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Marque</label>
                        <input type="text" name="marque" value="{{ old('marque', $eq?->marque ?? '') }}" placeholder="Dell, HP, Lenovo…"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>

                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Modèle</label>
                        <input type="text" name="modele" value="{{ old('modele', $eq?->modele ?? '') }}" placeholder="Latitude 5420"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>

                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Numéro de série</label>
                        <input type="text" name="numero_serie" value="{{ old('numero_serie', $eq?->numero_serie ?? '') }}"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>

                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Adresse MAC</label>
                        <input type="text" name="adresse_mac" value="{{ old('adresse_mac', $eq?->adresse_mac ?? '') }}" placeholder="AA:BB:CC:DD:EE:FF"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:'DM Mono',monospace;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>

                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Adresse IP</label>
                        <input type="text" name="adresse_ip" value="{{ old('adresse_ip', $eq?->adresse_ip ?? '') }}" placeholder="192.168.1.100"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:'DM Mono',monospace;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                </div>
            </div>

            {{-- Spécifications techniques --}}
            <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Spécifications techniques</h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Processeur (CPU)</label>
                        <input type="text" name="spec_cpu" value="{{ old('spec_cpu', $specs['cpu'] ?? '') }}" placeholder="Intel Core i7-1165G7"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Mémoire RAM</label>
                        <input type="text" name="spec_ram" value="{{ old('spec_ram', $specs['ram'] ?? '') }}" placeholder="16 Go DDR4"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Stockage</label>
                        <input type="text" name="spec_stockage" value="{{ old('spec_stockage', $specs['stockage'] ?? '') }}" placeholder="512 Go SSD NVMe"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Système d'exploitation</label>
                        <input type="text" name="spec_os" value="{{ old('spec_os', $specs['os'] ?? '') }}" placeholder="Windows 11 Pro"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Notes</h3>
                <textarea name="notes" rows="4" placeholder="Observations, historique de pannes, configurations particulières…"
                    style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit; resize:vertical;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">{{ old('notes', $eq?->notes ?? '') }}</textarea>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div style="display:flex; flex-direction:column; gap:1.5rem;">

            {{-- Affectation --}}
            <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Affectation</h3>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Utilisateur affecté</label>
                    <select name="assigned_user_id"
                        style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; background:#fff; font-family:inherit; cursor:pointer; outline:none;"
                        onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                        <option value="">— Non affecté —</option>
                        @foreach($utilisateurs as $u)
                            <option value="{{ $u->id }}" {{ old('assigned_user_id', $eq?->assigned_user_id ?? '') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->role_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:1rem;">
                    <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Localisation</label>
                    <input type="text" name="localisation" value="{{ old('localisation', $eq?->localisation ?? '') }}" placeholder="Bureau 2A, Salle serveurs…"
                        style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                        onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                </div>

                <div>
                    <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Département</label>
                    <input type="text" name="departement" value="{{ old('departement', $eq?->departement ?? '') }}" placeholder="Comptabilité, RH…"
                        style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                        onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                </div>
            </div>

            {{-- Achat & Garantie --}}
            <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <h3 style="font-size:.82rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Achat & Garantie</h3>

                <div style="display:flex; flex-direction:column; gap:1rem;">
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Date d'achat</label>
                        <input type="date" name="date_achat" value="{{ old('date_achat', $eq?->date_achat?->format('Y-m-d') ?? '') }}"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Prix d'achat (FCFA)</label>
                        <input type="number" name="prix_achat" value="{{ old('prix_achat', $eq?->prix_achat ?? '') }}" min="0"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Fournisseur</label>
                        <input type="text" name="fournisseur" value="{{ old('fournisseur', $eq?->fournisseur ?? '') }}"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">N° bon de commande</label>
                        <input type="text" name="numero_bon_commande" value="{{ old('numero_bon_commande', $eq?->numero_bon_commande ?? '') }}"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                    <div>
                        <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">Fin de garantie</label>
                        <input type="date" name="fin_garantie" value="{{ old('fin_garantie', $eq?->fin_garantie?->format('Y-m-d') ?? '') }}"
                            style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                            onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div style="background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">
                <button type="submit" form="form-equipement"
                    style="width:100%; padding:.75rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.9rem; font-weight:700; cursor:pointer; font-family:inherit; margin-bottom:.75rem;"
                    onmouseover="this.style.background='#7180A8'" onmouseout="this.style.background='#889ABF'">
                    {{ $isEdit ? '💾 Enregistrer les modifications' : '+ Ajouter au parc' }}
                </button>
                <a href="{{ $isEdit ? route('parc.show', $eq) : route('parc.index') }}"
                    style="display:block; text-align:center; padding:.65rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; font-weight:600; color:#6B7280; text-decoration:none;">
                    Annuler
                </a>

                @if($isEdit && (auth()->user()->isAdministrateur() || auth()->user()->isResponsableIT()))
                <div style="margin-top:1rem; padding-top:1rem; border-top:1px solid #F3F4F6;">
                    <button type="button"
                        onclick="document.getElementById('form-delete').submit()"
                        style="width:100%; padding:.65rem; background:#FEF2F2; color:#EF4444; border:1px solid #FCA5A5; border-radius:8px; font-size:.85rem; font-weight:600; cursor:pointer; font-family:inherit;">
                        Retirer du parc
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</form>

{{-- Formulaire de suppression séparé (hors du form principal) --}}
@if($isEdit && (auth()->user()->isAdministrateur() || auth()->user()->isResponsableIT()))
<form id="form-delete" method="POST" action="{{ route('parc.destroy', $eq) }}"
    onsubmit="return confirm('Retirer {{ $eq->code_inventaire }} du parc ? Cette action est irréversible.')">
    @csrf
    @method('DELETE')
</form>
@endif
@endsection
