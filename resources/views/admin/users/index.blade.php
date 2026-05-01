@extends('layouts.app')
@section('title', 'Gestion des utilisateurs')
@section('page-title', 'Gestion des utilisateurs')
@section('page-subtitle', $users->total() . ' utilisateur(s)')

@section('topbar-actions')
<a href="{{ route('admin.users.create') }}" class="topbar-btn btn-primary">
    <i class="fa-solid fa-user-plus"></i> Nouvel utilisateur
</a>
@endsection

@section('content')
<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Département</th>
                    <th>Statut</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--brand-pale);color:var(--brand-dark);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <span style="font-weight:500;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:13px;color:var(--gray-500);">{{ $user->email }}</td>
                    <td>
                        <span class="badge" style="background:var(--brand-pale);color:var(--brand-dark);">{{ $user->role_label }}</span>
                    </td>
                    <td style="font-size:13px;color:var(--gray-500);">{{ $user->department ?? '—' }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge" style="background:#EBF9F4;color:var(--success);">Actif</span>
                        @else
                            <span class="badge" style="background:#FDEEEE;color:var(--danger);">Inactif</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--gray-400);">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.users.edit', $user) }}" class="topbar-btn btn-outline btn-sm" title="Modifier">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="topbar-btn btn-sm {{ $user->is_active ? 'btn-warning' : 'btn-success' }}" title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                    <i class="fa-solid fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--gray-400);">Aucun utilisateur</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div style="padding:16px 22px;border-top:1px solid var(--gray-100);">{{ $users->links() }}</div>
    @endif
</div>
@endsection
