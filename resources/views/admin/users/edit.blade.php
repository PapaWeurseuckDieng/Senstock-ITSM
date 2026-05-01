@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Modifier l'utilisateur</h1>
        <p class="page-subtitle">Mettre à jour les informations de {{ $user->name }}</p>
    </div>
    <a href="{{ route('admin.users.index') }}" style="display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.2rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; font-weight:600; color:#6B7280; text-decoration:none; background:#fff;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Retour
    </a>
</div>

@if($errors->any())
<div style="background:#FEF2F2; border:1px solid #FCA5A5; border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.5rem;">
    <p style="font-weight:600; color:#DC2626; margin:0 0 .5rem;">Veuillez corriger les erreurs suivantes :</p>
    <ul style="margin:0; padding-left:1.25rem; color:#DC2626; font-size:.85rem;">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div style="max-width:640px; background:#fff; border-radius:12px; padding:2rem; box-shadow:0 2px 8px rgba(0,0,0,.06);">

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        {{-- Informations personnelles --}}
        <div style="margin-bottom:1.75rem; padding-bottom:1.75rem; border-bottom:1px solid #F3F4F6;">
            <h3 style="font-size:.85rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Informations personnelles</h3>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">
                    Nom complet <span style="color:#EF4444;">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    style="width:100%; padding:.65rem .9rem; border:1px solid {{ $errors->has('name') ? '#FCA5A5' : '#E5E7EB' }}; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                @error('name')<p style="color:#EF4444; font-size:.75rem; margin:.3rem 0 0;">{{ $message }}</p>@enderror
            </div>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">
                    Adresse email <span style="color:#EF4444;">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                    style="width:100%; padding:.65rem .9rem; border:1px solid {{ $errors->has('email') ? '#FCA5A5' : '#E5E7EB' }}; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                @error('email')<p style="color:#EF4444; font-size:.75rem; margin:.3rem 0 0;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">
                    Numéro de téléphone
                </label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    placeholder="+221 77 000 00 00"
                    style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
            </div>
        </div>

        {{-- Rôle --}}
        <div style="margin-bottom:1.75rem; padding-bottom:1.75rem; border-bottom:1px solid #F3F4F6;">
            <h3 style="font-size:.85rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1.25rem;">Rôle et permissions</h3>

            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">
                    Rôle <span style="color:#EF4444;">*</span>
                </label>
                <select name="role" required
                    style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; background:#fff; font-family:inherit; cursor:pointer;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                    <option value="utilisateur" {{ old('role', $user->role) === 'utilisateur' ? 'selected' : '' }}>Utilisateur</option>
                    <option value="technicien" {{ old('role', $user->role) === 'technicien' ? 'selected' : '' }}>Technicien IT</option>
                    <option value="responsable_it" {{ old('role', $user->role) === 'responsable_it' ? 'selected' : '' }}>Responsable IT</option>
                    <option value="administrateur" {{ old('role', $user->role) === 'administrateur' ? 'selected' : '' }}>Administrateur</option>
                </select>
                @error('role')<p style="color:#EF4444; font-size:.75rem; margin:.3rem 0 0;">{{ $message }}</p>@enderror

                <div style="margin-top:.75rem; padding:.75rem; background:#F0F4FF; border-radius:8px; border-left:3px solid #889ABF;">
                    <p style="font-size:.78rem; color:#4B5563; margin:0; line-height:1.5;">
                        <strong style="color:#1A1F2E;">Utilisateur :</strong> Création et suivi de tickets uniquement.<br>
                        <strong style="color:#1A1F2E;">Technicien IT :</strong> Traitement des tickets, mise à jour des statuts.<br>
                        <strong style="color:#1A1F2E;">Responsable IT :</strong> Supervision, KPI, rapports, affectation.<br>
                        <strong style="color:#1A1F2E;">Administrateur :</strong> Accès complet, gestion des utilisateurs et audit.
                    </p>
                </div>
            </div>
        </div>

        {{-- Mot de passe --}}
        <div style="margin-bottom:1.75rem; padding-bottom:1.75rem; border-bottom:1px solid #F3F4F6;">
            <h3 style="font-size:.85rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 .25rem;">Mot de passe</h3>
            <p style="font-size:.78rem; color:#9CA3AF; margin:0 0 1.25rem;">Laisser vide pour conserver le mot de passe actuel.</p>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">
                    Nouveau mot de passe
                </label>
                <input type="password" name="password" id="password"
                    placeholder="Minimum 8 caractères"
                    style="width:100%; padding:.65rem .9rem; border:1px solid {{ $errors->has('password') ? '#FCA5A5' : '#E5E7EB' }}; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                @error('password')<p style="color:#EF4444; font-size:.75rem; margin:.3rem 0 0;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label style="display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:.4rem;">
                    Confirmer le nouveau mot de passe
                </label>
                <input type="password" name="password_confirmation"
                    placeholder="Répéter le mot de passe"
                    style="width:100%; padding:.65rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; outline:none; box-sizing:border-box; font-family:inherit;"
                    onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
            </div>
        </div>

        {{-- Statut --}}
        <div style="margin-bottom:2rem;">
            <h3 style="font-size:.85rem; font-weight:700; color:#889ABF; text-transform:uppercase; letter-spacing:.08em; margin:0 0 1rem;">Statut du compte</h3>

            <label style="display:flex; align-items:center; gap:.75rem; cursor:pointer;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                    style="width:18px; height:18px; accent-color:#889ABF; cursor:pointer;">
                <div>
                    <p style="font-size:.85rem; font-weight:600; color:#374151; margin:0;">Compte actif</p>
                    <p style="font-size:.75rem; color:#9CA3AF; margin:.1rem 0 0;">Un compte inactif ne peut plus se connecter à la plateforme.</p>
                </div>
            </label>
        </div>

        {{-- Actions --}}
        <div style="display:flex; justify-content:flex-end; gap:.75rem;">
            <a href="{{ route('admin.users.index') }}"
                style="padding:.65rem 1.5rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.88rem; font-weight:600; color:#6B7280; text-decoration:none; background:#fff;">
                Annuler
            </a>
            <button type="submit"
                style="padding:.65rem 1.75rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.88rem; font-weight:600; cursor:pointer; font-family:inherit;"
                onmouseover="this.style.background='#7180A8'" onmouseout="this.style.background='#889ABF'">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
