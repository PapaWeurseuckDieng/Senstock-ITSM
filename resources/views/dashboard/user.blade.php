{{-- resources/views/dashboard/user.blade.php --}}
@extends('layouts.app')
@section('title', 'Mon espace')
@section('page-title', 'Mon espace')
@section('page-subtitle', 'Bienvenue, ' . auth()->user()->name)
@section('content')

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon brand"><i class="fa-solid fa-ticket"></i></div>
        <div><div class="stat-value">{{ $stats['total'] }}</div><div class="stat-label">Mes tickets</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info"><i class="fa-solid fa-circle-dot"></i></div>
        <div><div class="stat-value">{{ $stats['ouverts'] }}</div><div class="stat-label">Ouverts</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-spinner"></i></div>
        <div><div class="stat-value">{{ $stats['en_cours'] }}</div><div class="stat-label">En cours</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="fa-solid fa-circle-check"></i></div>
        <div><div class="stat-value">{{ $stats['resolus'] }}</div><div class="stat-label">Résolus</div></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Mes tickets récents</span>
        <a href="{{ route('tickets.index') }}" class="topbar-btn btn-outline btn-sm">Voir tout</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>N° Ticket</th><th>Titre</th><th>Type</th><th>Priorité</th><th>Statut</th><th>Assigné à</th><th>Date</th></tr>
            </thead>
            <tbody>
                @forelse($recentTickets as $ticket)
                <tr>
                    <td><a href="{{ route('tickets.show', $ticket) }}" class="ticket-link"><span class="ticket-number">{{ $ticket->ticket_number }}</span></a></td>
                    <td><a href="{{ route('tickets.show', $ticket) }}" class="ticket-link" style="font-weight:500;">{{ Str::limit($ticket->title, 50) }}</a></td>
                    <td><span class="badge badge-{{ $ticket->type }}">{{ $ticket->type_label }}</span></td>
                    <td><span class="badge badge-{{ $ticket->priority }}">{{ $ticket->priority_label }}</span></td>
                    <td><span class="badge badge-{{ $ticket->status }}">{{ $ticket->status_label }}</span></td>
                    <td style="font-size:12px;color:var(--gray-400);">{{ $ticket->assignee?->name ?? 'Non assigné' }}</td>
                    <td style="font-size:12px;color:var(--gray-400);">{{ $ticket->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--gray-400);padding:32px;">Aucun ticket. <a href="{{ route('tickets.create') }}" style="color:var(--brand);">Créer votre premier ticket</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
