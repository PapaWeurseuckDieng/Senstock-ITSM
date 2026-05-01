@extends('layouts.app')
@section('title', 'Nouveau ticket')
@section('page-title', 'Créer un ticket')
@section('page-subtitle', 'Décrivez votre demande ou incident')

@section('content')

<div style="max-width: 800px;">
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-plus-circle" style="color:var(--brand);margin-right:8px;"></i>Nouveau ticket</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="title">
                        Titre <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}"
                        placeholder="Décrivez brièvement le problème..."
                        required
                    >
                    @error('title')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div class="form-group">
                        <label class="form-label" for="type">
                            Type <span class="required">*</span>
                        </label>
                        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Sélectionner...</option>
                            <option value="incident"   {{ old('type') == 'incident'   ? 'selected' : '' }}>🔴 Incident</option>
                            <option value="panne"      {{ old('type') == 'panne'      ? 'selected' : '' }}>⚡ Panne</option>
                            <option value="demande"    {{ old('type') == 'demande'    ? 'selected' : '' }}>📋 Demande</option>
                            <option value="changement" {{ old('type') == 'changement' ? 'selected' : '' }}>🔄 Changement</option>
                        </select>
                        @error('type')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="priority">
                            Priorité <span class="required">*</span>
                        </label>
                        <select id="priority" name="priority" class="form-select @error('priority') is-invalid @enderror" required>
                            <option value="">Sélectionner...</option>
                            <option value="critique" {{ old('priority') == 'critique' ? 'selected' : '' }}>🔴 Critique (SLA: 4h)</option>
                            <option value="haute"    {{ old('priority') == 'haute'    ? 'selected' : '' }}>🟠 Haute (SLA: 8h)</option>
                            <option value="normale"  {{ old('priority') == 'normale'  ? 'selected' : '' }}>🔵 Normale (SLA: 24h)</option>
                            <option value="faible"   {{ old('priority') == 'faible'   ? 'selected' : '' }}>⚪ Faible (SLA: 72h)</option>
                        </select>
                        @error('priority')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category">
                            Catégorie <span class="required">*</span>
                        </label>
                        <select id="category" name="category" class="form-select @error('category') is-invalid @enderror" required>
                            <option value="">Sélectionner...</option>
                            <option value="materiel"  {{ old('category') == 'materiel'  ? 'selected' : '' }}>💻 Matériel</option>
                            <option value="logiciel"  {{ old('category') == 'logiciel'  ? 'selected' : '' }}>🖥 Logiciel</option>
                            <option value="reseau"    {{ old('category') == 'reseau'    ? 'selected' : '' }}>🌐 Réseau</option>
                            <option value="acces"     {{ old('category') == 'acces'     ? 'selected' : '' }}>🔑 Accès / Droits</option>
                            <option value="autre"     {{ old('category') == 'autre'     ? 'selected' : '' }}>📦 Autre</option>
                        </select>
                        @error('category')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">
                        Description détaillée <span class="required">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        class="form-control @error('description') is-invalid @enderror"
                        rows="6"
                        placeholder="Décrivez en détail le problème rencontré : que s'est-il passé ? Quand ? Quel est l'impact sur votre travail ?"
                        required
                    >{{ old('description') }}</textarea>
                    @error('description')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="attachments">
                        Pièces jointes
                        <span style="color:var(--gray-400);font-weight:400;">(optionnel — max 5 fichiers, 10Mo chacun)</span>
                    </label>
                    <div style="border:2px dashed var(--gray-200);border-radius:var(--radius-sm);padding:20px;text-align:center;background:var(--gray-50);">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:24px;color:var(--gray-300);margin-bottom:8px;display:block;"></i>
                        <input type="file" id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg,.gif,.txt,.zip" style="display:none;">
                        <label for="attachments" style="cursor:pointer;color:var(--brand);font-weight:500;">Cliquez pour sélectionner</label>
                        <span style="color:var(--gray-400);"> ou glissez-déposez vos fichiers</span>
                        <div id="file-list" style="margin-top:10px;"></div>
                    </div>
                    @error('attachments.*')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- SLA info --}}
                <div id="sla-info" style="display:none;background:var(--brand-pale);border:1px solid var(--brand-light);border-radius:var(--radius-sm);padding:12px 16px;margin-bottom:18px;">
                    <i class="fa-solid fa-clock" style="color:var(--brand);"></i>
                    <span id="sla-text" style="font-size:13px;color:var(--brand-dark);font-weight:500;"></span>
                </div>

                <div style="display:flex;gap:12px;justify-content:flex-end;">
                    <a href="{{ route('tickets.index') }}" class="topbar-btn btn-outline">Annuler</a>
                    <button type="submit" class="topbar-btn btn-primary">
                        <i class="fa-solid fa-paper-plane"></i>
                        Soumettre le ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const slaHours = { critique: 4, haute: 8, normale: 24, faible: 72 };

    document.getElementById('priority').addEventListener('change', function() {
        const hours = slaHours[this.value];
        const info  = document.getElementById('sla-info');
        const text  = document.getElementById('sla-text');
        if (hours) {
            text.textContent = `SLA : Ce ticket doit être résolu dans ${hours} heure${hours > 1 ? 's' : ''}.`;
            info.style.display = 'block';
        } else {
            info.style.display = 'none';
        }
    });

    document.getElementById('attachments').addEventListener('change', function() {
        const list = document.getElementById('file-list');
        list.innerHTML = '';
        Array.from(this.files).forEach(file => {
            const div = document.createElement('div');
            div.style.cssText = 'font-size:12px;color:var(--gray-600);padding:4px 0;display:flex;align-items:center;gap:6px;';
            div.innerHTML = `<i class="fa-solid fa-file" style="color:var(--brand);"></i> ${file.name} <span style="color:var(--gray-400);">(${(file.size / 1024).toFixed(0)} Ko)</span>`;
            list.appendChild(div);
        });
    });
</script>
@endpush
@endsection
