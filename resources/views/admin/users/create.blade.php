@extends('layouts.app')
@section('title', 'Nouvel utilisateur')
@section('page-title', 'Créer un utilisateur')

@section('content')
<div style="max-width:600px;">
    <div class="card">
        <div class="card-header"><span class="card-title">Informations de l'utilisateur</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Nom complet <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Rôle <span class="required">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Sélectionner...</option>
                            <option value="utilisateur"   {{ old('role') == 'utilisateur'   ? 'selected' : '' }}>Utilisateur</option>
                            <option value="technicien"    {{ old('role') == 'technicien'    ? 'selected' : '' }}>Technicien IT</option>
                            <option value="responsable_it"{{ old('role') == 'responsable_it'? 'selected' : '' }}>Responsable IT</option>
                            <option value="administrateur"{{ old('role') == 'administrateur'? 'selected' : '' }}>Administrateur</option>
                        </select>
                        @error('role')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Département</label>
                        <input type="text" name="department" class="form-control" value="{{ old('department') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mot de passe <span class="required">*</span></label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                        @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirmer le mot de passe <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
                    <a href="{{ route('admin.users.index') }}" class="topbar-btn btn-outline">Annuler</a>
                    <button type="submit" class="topbar-btn btn-primary"><i class="fa-solid fa-user-plus"></i> Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
