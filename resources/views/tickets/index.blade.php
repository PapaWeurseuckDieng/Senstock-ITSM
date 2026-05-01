@extends('layouts.app')
@section('title', 'Tickets')
@section('page-title', 'Gestion des tickets')
@section('page-subtitle', $tickets->total() . ' ticket(s) trouvé(s)')

@section('content')

{{-- Filtres --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-body" style="padding:16px 22px;">
        <form method="GET" action="{{ route('tickets.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:2;min-width:200px;">
                <label class="form-label" style="margin-bottom:5px;">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="N° ticket, titre, description...">
            </div>
            <div style="min-width:140px;">
                <label class="form-label" style="margin-bottom:5px;">Statut</label>
                <select name="status" class="form-select">
                    <option value="">Tous les statuts</option>
                    @foreach(['ouvert'=>'Ouvert','en_cours'=>'En cours','en_attente'=>'En attente','resolu'=>'Résolu','ferme'=>'Fermé','annule'=>'Annulé'] as $val=>$label)
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:140px;">
                <label class="form-label" style="margin-bottom:5px;">Priorité</label>
                <select name="priority" class="form-select">
                    <option value="">Toutes</option>
                    @foreach(['critique'=>'Critique','haute'=>'Haute','normale'=>'Normale','faible'=>'Faible'] as $val=>$label)
                        <option value="{{ $val }}" {{ request('priority') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:140px;">
                <label class="form-label" style="margin-bottom:5px;">Type</label>
                <select name="type" class="form-select">
                    <option value="">Tous</option>
                    @foreach(['incident'=>'Incident','panne'=>'Panne','demande'=>'Demande','changement'=>'Changement'] as $val=>$label)
                        <option value="{{ $val }}" {{ request('type') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="topbar-btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Filtrer
                </button>
                <a href="{{ route('tickets.index') }}" class="topbar-btn btn-outline">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

{{-- Liste --}}
<div class="card">
    <div class="card-header">
        <span class="card-title"><i class="fa-solid fa-list" style="color:var(--brand);margin-right:8px;"></i>Liste des tickets</span>
        @if(auth()->user()->isITStaff())
        <div style="display:flex;gap:8px;">
            <a href="{{ route('reports.csv', request()->query()) }}" class="topbar-btn btn-outline btn-sm">
                <i class="fa-solid fa-file-csv"></i> CSV
            </a>
            <a href="{{ route('reports.pdf', request()->query()) }}" class="topbar-btn btn-outline btn-sm">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
        </div>
        @endif
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>N° Ticket</th>
                    <th>Titre</th>
                    @if(auth()->user()->isITStaff())<th>Demandeur</th>@endif
                    <th>Type</th>
                    <th>Priorité</th>
                    <th>Statut</th>
                    <th>Assigné à</th>
                    <th>SLA</th>
                    <th>Créé le</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr class="{{ $ticket->is_overdue && !in_array($ticket->status, ['resolu','ferme','annule']) ? 'overdue-row' : '' }}">
                    <td>
                        <span class="ticket-number">{{ $ticket->ticket_number }}</span>
                        @if($ticket->type === 'panne')
                            <span style="color:var(--danger);font-size:11px;margin-left:4px;" title="Panne signalée"><i class="fa-solid fa-bolt"></i></span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}" class="ticket-link" style="font-weight:500;color:var(--gray-800);">
                            {{ Str::limit($ticket->title, 50) }}
                        </a>
                    </td>
                    @if(auth()->user()->isITStaff())
                    <td style="font-size:12px;color:var(--gray-500);">{{ $ticket->user->name }}</td>
                    @endif
                    <td><span class="badge badge-{{ $ticket->type }}">{{ $ticket->type_label }}</span></td>
                    <td><span class="badge badge-{{ $ticket->priority }}">{{ $ticket->priority_label }}</span></td>
                    <td><span class="badge badge-{{ $ticket->status }}">{{ $ticket->status_label }}</span></td>
                    <td style="font-size:12px;color:var(--gray-500);">
                        {{ $ticket->assignee?->name ?? '<span style="color:var(--gray-300);">—</span>' }}
                    </td>
                    <td>
                        @if($ticket->sla_breached)
                            <span class="sla-breach" title="SLA dépassé"><i class="fa-solid fa-circle-exclamation"></i> Dépassé</span>
                        @elseif($ticket->is_overdue)
                            <span class="sla-warning" title="En retard"><i class="fa-solid fa-triangle-exclamation"></i> Retard</span>
                        @elseif($ticket->sla_deadline)
                            <span class="sla-ok" title="{{ $ticket->sla_deadline->format('d/m H:i') }}">
                                <i class="fa-solid fa-check"></i> {{ $ticket->sla_deadline->diffForHumans() }}
                            </span>
                        @else
                            <span style="color:var(--gray-300);">—</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--gray-400);">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('tickets.show', $ticket) }}" class="topbar-btn btn-outline btn-sm">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:48px;color:var(--gray-400);">
                        <i class="fa-regular fa-folder-open" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucun ticket trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--gray-100);">
        {{ $tickets->links() }}
    </div>
    @endif
</div>

@push('styles')
<style>
.overdue-row { background: rgba(232,85,85,.03); }
.overdue-row:hover { background: rgba(232,85,85,.06) !important; }
</style>
@endpush
@endsection
