@extends('layouts.app')
@section('title', $ticket->ticket_number)
@section('page-title', $ticket->ticket_number)
@section('page-subtitle', $ticket->title)

@section('topbar-actions')
<a href="{{ route('tickets.index') }}" class="topbar-btn btn-outline btn-sm">
    <i class="fa-solid fa-arrow-left"></i> Retour
</a>
@endsection

@section('content')

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">

    {{-- Colonne principale --}}
    <div>
        {{-- Infos ticket --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <div>
                    <span class="card-title">{{ $ticket->title }}</span>
                    <div style="margin-top:6px;display:flex;gap:8px;flex-wrap:wrap;">
                        <span class="badge badge-{{ $ticket->type }}">{{ $ticket->type_label }}</span>
                        <span class="badge badge-{{ $ticket->priority }}">{{ $ticket->priority_label }}</span>
                        <span class="badge badge-{{ $ticket->status }}">{{ $ticket->status_label }}</span>
                        @if($ticket->sla_breached)
                            <span class="badge" style="background:#FDEEEE;color:var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> SLA Dépassé</span>
                        @endif
                    </div>
                </div>
                <span class="ticket-number" style="font-size:14px;">{{ $ticket->ticket_number }}</span>
            </div>
            <div class="card-body">
                <div style="white-space:pre-wrap;line-height:1.7;color:var(--gray-700);font-size:14px;">{{ $ticket->description }}</div>

                @if($ticket->attachments->isNotEmpty())
                <div style="margin-top:20px;padding-top:20px;border-top:1px solid var(--gray-100);">
                    <div style="font-size:12px;font-weight:600;color:var(--gray-400);text-transform:uppercase;letter-spacing:1px;margin-bottom:10px;">Pièces jointes</div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        @foreach($ticket->attachments as $att)
                        <a href="{{ Storage::url($att->path) }}" target="_blank" class="topbar-btn btn-outline btn-sm">
                            <i class="fa-solid fa-paperclip"></i>
                            {{ $att->original_filename }}
                            <span style="color:var(--gray-400);">({{ $att->file_size_human }})</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Note de résolution --}}
        @if($ticket->resolution_note)
        <div class="card" style="margin-bottom:20px;border-left:4px solid var(--success);">
            <div class="card-header">
                <span class="card-title" style="color:var(--success);"><i class="fa-solid fa-circle-check" style="margin-right:8px;"></i>Résolution</span>
            </div>
            <div class="card-body">
                <div style="white-space:pre-wrap;line-height:1.7;color:var(--gray-700);font-size:14px;">{{ $ticket->resolution_note }}</div>
                @if($ticket->t4_resolved_at)
                <div style="margin-top:10px;font-size:12px;color:var(--gray-400);">
                    Résolu le {{ $ticket->t4_resolved_at->format('d/m/Y à H:i') }}
                    @if($ticket->assignee) par <strong>{{ $ticket->assignee->name }}</strong> @endif
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Validation utilisateur --}}
        @if($ticket->status === 'resolu' && !$ticket->user_validated && $ticket->user_id === auth()->id())
        <div class="card" style="margin-bottom:20px;border-left:4px solid var(--brand);">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-star" style="color:var(--brand);margin-right:8px;"></i>Valider la résolution</span>
            </div>
            <div class="card-body">
                <p style="font-size:14px;color:var(--gray-600);margin-bottom:16px;">Le ticket a été marqué comme résolu. Veuillez confirmer si votre problème est bien résolu.</p>
                <form method="POST" action="{{ route('tickets.validate', $ticket) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Satisfaction <span class="required">*</span></label>
                        <div style="display:flex;gap:8px;">
                            @for($i=1;$i<=5;$i++)
                            <label style="cursor:pointer;text-align:center;">
                                <input type="radio" name="satisfaction_score" value="{{ $i }}" style="display:none;" required class="star-radio">
                                <div class="star-btn" data-val="{{ $i }}" style="width:44px;height:44px;border:2px solid var(--gray-200);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:20px;transition:all .2s;">{{ $i <= 2 ? '😞' : ($i == 3 ? '😐' : ($i == 4 ? '🙂' : '😄')) }}</div>
                                <div style="font-size:11px;color:var(--gray-400);margin-top:4px;">{{ $i }}/5</div>
                            </label>
                            @endfor
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Commentaire (optionnel)</label>
                        <textarea name="satisfaction_comment" class="form-control" rows="3" placeholder="Vos remarques sur la résolution..."></textarea>
                    </div>
                    <div style="display:flex;gap:10px;">
                        <button type="submit" class="topbar-btn btn-success">
                            <i class="fa-solid fa-check"></i> Confirmer la résolution
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Résoudre (technicien) --}}
        @if(in_array($ticket->status, ['ouvert','en_cours','en_attente']) && auth()->user()->isITStaff())
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-wrench" style="color:var(--brand);margin-right:8px;"></i>Résoudre le ticket</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.resolve', $ticket) }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Note de résolution <span class="required">*</span></label>
                        <textarea name="resolution_note" class="form-control" rows="4" placeholder="Décrivez la solution apportée..." required minlength="10"></textarea>
                    </div>
                    <button type="submit" class="topbar-btn btn-success">
                        <i class="fa-solid fa-circle-check"></i> Marquer comme résolu
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Commentaires --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-comments" style="color:var(--brand);margin-right:8px;"></i>Commentaires <span style="color:var(--gray-400);font-weight:400;">({{ $ticket->comments->count() }})</span></span>
            </div>
            <div class="card-body" style="padding:0;">
                @forelse($ticket->comments as $comment)
                <div style="padding:18px 22px;border-bottom:1px solid var(--gray-100);{{ $comment->is_internal ? 'background:rgba(136,154,191,.05);' : '' }}">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--brand-pale);color:var(--brand-dark);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;">
                            {{ strtoupper(substr($comment->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <span style="font-weight:500;font-size:13px;">{{ $comment->user->name }}</span>
                            <span style="color:var(--gray-400);font-size:11px;margin-left:8px;">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                            @if($comment->is_internal)
                                <span class="badge" style="background:var(--brand-pale);color:var(--brand-dark);margin-left:6px;font-size:10px;">Note interne</span>
                            @endif
                        </div>
                    </div>
                    <div style="font-size:14px;color:var(--gray-700);line-height:1.6;white-space:pre-wrap;margin-left:42px;">{{ $comment->content }}</div>
                </div>
                @empty
                <div style="padding:32px;text-align:center;color:var(--gray-400);font-size:14px;">Aucun commentaire pour le moment.</div>
                @endforelse

                @if(!in_array($ticket->status, ['ferme','annule']))
                <div style="padding:20px 22px;background:var(--gray-50);border-top:1px solid var(--gray-100);">
                    <form method="POST" action="{{ route('tickets.comment', $ticket) }}">
                        @csrf
                        <textarea name="content" class="form-control" rows="3" placeholder="Ajouter un commentaire..." required minlength="2" style="margin-bottom:10px;"></textarea>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <button type="submit" class="topbar-btn btn-primary btn-sm">
                                <i class="fa-solid fa-paper-plane"></i> Commenter
                            </button>
                            @if(auth()->user()->isITStaff())
                            <label style="display:flex;align-items:center;gap:6px;font-size:13px;color:var(--gray-500);cursor:pointer;">
                                <input type="checkbox" name="is_internal" value="1" style="accent-color:var(--brand);">
                                Note interne (visible par l'IT uniquement)
                            </label>
                            @endif
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Colonne latérale --}}
    <div>
        {{-- Informations --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><span class="card-title">Informations</span></div>
            <div class="card-body" style="padding:0;">
                @php
                $infos = [
                    ['label' => 'Demandeur', 'value' => $ticket->user->name, 'icon' => 'fa-user'],
                    ['label' => 'Département', 'value' => $ticket->user->department ?? '—', 'icon' => 'fa-building'],
                    ['label' => 'Catégorie', 'value' => ucfirst($ticket->category), 'icon' => 'fa-tag'],
                    ['label' => 'Assigné à', 'value' => $ticket->assignee?->name ?? 'Non assigné', 'icon' => 'fa-user-gear'],
                    ['label' => 'Deadline SLA', 'value' => $ticket->sla_deadline?->format('d/m/Y H:i') ?? '—', 'icon' => 'fa-clock'],
                ];
                @endphp
                @foreach($infos as $info)
                <div style="display:flex;align-items:center;gap:12px;padding:13px 18px;border-bottom:1px solid var(--gray-100);">
                    <i class="fa-solid {{ $info['icon'] }}" style="color:var(--brand-light);width:16px;text-align:center;flex-shrink:0;"></i>
                    <div>
                        <div style="font-size:11px;color:var(--gray-400);font-weight:500;text-transform:uppercase;letter-spacing:.5px;">{{ $info['label'] }}</div>
                        <div style="font-size:13px;font-weight:500;color:var(--gray-700);">{{ $info['value'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Actions technicien/responsable --}}
        @if(auth()->user()->isITStaff())
        <div class="card" style="margin-bottom:16px;">
            <div class="card-header"><span class="card-title">Actions</span></div>
            <div class="card-body">
                {{-- Assigner --}}
                @if(in_array($ticket->status, ['ouvert','en_cours','en_attente']))
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" style="margin-bottom:14px;">
                    @method('PATCH')
                    @csrf
                    <label class="form-label">Assigner à un technicien</label>
                    <select name="assigned_to" class="form-select" style="margin-bottom:8px;" required>
                        <option value="">Sélectionner un technicien...</option>
                        @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ $ticket->assigned_to == $tech->id ? 'selected' : '' }}>
                            {{ $tech->name }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="topbar-btn btn-primary btn-sm" style="width:100%;">
                        <i class="fa-solid fa-user-check"></i> Assigner
                    </button>
                </form>
                @endif

                {{-- Changer statut --}}
                @if(!in_array($ticket->status, ['ferme','annule']))
                <form method="POST" action="{{ route('tickets.updateStatus', $ticket) }}">
                    @csrf
                    <label class="form-label">Changer le statut</label>
                    <select name="status" class="form-select" style="margin-bottom:8px;">
                        @foreach(['en_cours'=>'En cours','en_attente'=>'En attente','annule'=>'Annuler'] as $val=>$label)
                        <option value="{{ $val }}" {{ $ticket->status == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="topbar-btn btn-outline btn-sm" style="width:100%;">
                        <i class="fa-solid fa-arrows-rotate"></i> Mettre à jour
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif

        {{-- Horodatage --}}
        <div class="card">
            <div class="card-header"><span class="card-title"><i class="fa-solid fa-timeline" style="color:var(--brand);margin-right:8px;"></i>Horodatage</span></div>
            <div class="card-body" style="padding:0;">
                @php
                $timestamps = [
                    ['label' => 'T0 — Création', 'value' => $ticket->t0_created_at],
                    ['label' => 'T1 — Assignation', 'value' => $ticket->t1_assigned_at],
                    ['label' => 'T2 — Prise en charge', 'value' => $ticket->t2_started_at],
                    ['label' => 'T3 — Mise en attente', 'value' => $ticket->t3_pending_at],
                    ['label' => 'T4 — Résolution', 'value' => $ticket->t4_resolved_at],
                    ['label' => 'T5 — Validation', 'value' => $ticket->t5_validated_at],
                    ['label' => 'T6 — Fermeture', 'value' => $ticket->t6_closed_at],
                ];
                @endphp
                @foreach($timestamps as $i => $ts)
                <div style="display:flex;align-items:flex-start;gap:12px;padding:11px 18px;border-bottom:{{ $i < 6 ? '1px solid var(--gray-100)' : 'none' }}">
                    <div style="width:24px;height:24px;border-radius:50%;background:{{ $ts['value'] ? 'var(--success)' : 'var(--gray-100)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        @if($ts['value'])
                            <i class="fa-solid fa-check" style="font-size:10px;color:white;"></i>
                        @else
                            <i class="fa-solid fa-minus" style="font-size:10px;color:var(--gray-300);"></i>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:600;color:var(--gray-500);">{{ $ts['label'] }}</div>
                        <div style="font-size:12px;color:{{ $ts['value'] ? 'var(--gray-700)' : 'var(--gray-300)' }};">
                            {{ $ts['value'] ? $ts['value']->format('d/m/Y H:i') : 'En attente' }}
                        </div>
                    </div>
                </div>
                @endforeach

                @if($ticket->resolution_time)
                <div style="padding:12px 18px;background:var(--brand-pale);border-top:1px solid var(--brand-light);">
                    <div style="font-size:11px;font-weight:600;color:var(--brand-dark);text-transform:uppercase;letter-spacing:.5px;">Durée totale</div>
                    <div style="font-size:14px;font-weight:700;color:var(--brand);">
                        @php
                            $hours = intdiv($ticket->resolution_time, 60);
                            $mins  = $ticket->resolution_time % 60;
                        @endphp
                        {{ $hours > 0 ? "{$hours}h " : '' }}{{ $mins }}min
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection