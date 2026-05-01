@extends('layouts.app')

@section('title', 'Journal d\'audit')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Journal d'audit</h1>
        <p class="page-subtitle">Traçabilité complète de toutes les actions sur la plateforme</p>
    </div>
    <div style="display:flex; gap:.75rem; align-items:center;">
        <a href="{{ route('reports.index') }}" style="display:inline-flex; align-items:center; gap:.5rem; padding:.6rem 1.2rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; font-weight:600; color:#6B7280; text-decoration:none; background:#fff;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Exporter
        </a>
    </div>
</div>

{{-- Info immuabilité --}}
<div style="background:#FFF7ED; border:1px solid #FED7AA; border-radius:10px; padding:.9rem 1.25rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:.75rem;">
    <svg width="18" height="18" fill="none" stroke="#F59E0B" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    <p style="font-size:.82rem; color:#92400E; margin:0;">
        <strong>Logs immuables :</strong> Les entrées du journal d'audit ne peuvent pas être modifiées ou supprimées. Chaque action est horodatée et signée.
    </p>
</div>

{{-- Filtres --}}
<div style="background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.5rem;">
    <form method="GET" action="{{ route('admin.audit') }}" style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr auto; gap:.75rem; align-items:end;">
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase; letter-spacing:.04em;">Rechercher</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Utilisateur, action, entité…"
                style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; outline:none; box-sizing:border-box; font-family:inherit;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
        </div>
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase; letter-spacing:.04em;">Action</label>
            <select name="action" style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; background:#fff; font-family:inherit; cursor:pointer; outline:none;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
                <option value="">Toutes</option>
                <option value="connexion" {{ request('action') === 'connexion' ? 'selected' : '' }}>Connexion</option>
                <option value="deconnexion" {{ request('action') === 'deconnexion' ? 'selected' : '' }}>Déconnexion</option>
                <option value="creation_ticket" {{ request('action') === 'creation_ticket' ? 'selected' : '' }}>Création ticket</option>
                <option value="modification_ticket" {{ request('action') === 'modification_ticket' ? 'selected' : '' }}>Modification ticket</option>
                <option value="affectation" {{ request('action') === 'affectation' ? 'selected' : '' }}>Affectation</option>
                <option value="resolution" {{ request('action') === 'resolution' ? 'selected' : '' }}>Résolution</option>
                <option value="creation_utilisateur" {{ request('action') === 'creation_utilisateur' ? 'selected' : '' }}>Création utilisateur</option>
            </select>
        </div>
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase; letter-spacing:.04em;">Du</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; outline:none; box-sizing:border-box; font-family:inherit;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
        </div>
        <div>
            <label style="display:block; font-size:.75rem; font-weight:600; color:#6B7280; margin-bottom:.35rem; text-transform:uppercase; letter-spacing:.04em;">Au</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                style="width:100%; padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; outline:none; box-sizing:border-box; font-family:inherit;"
                onfocus="this.style.borderColor='#889ABF'" onblur="this.style.borderColor='#E5E7EB'">
        </div>
        <div style="display:flex; gap:.5rem;">
            <button type="submit" style="padding:.6rem 1.2rem; background:#889ABF; color:#fff; border:none; border-radius:8px; font-size:.85rem; font-weight:600; cursor:pointer; font-family:inherit; white-space:nowrap;">
                Filtrer
            </button>
            @if(request()->hasAny(['search','action','date_from','date_to']))
            <a href="{{ route('admin.audit') }}" style="padding:.6rem .9rem; border:1px solid #E5E7EB; border-radius:8px; font-size:.85rem; color:#6B7280; text-decoration:none; background:#fff; display:flex; align-items:center;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Tableau des logs --}}
<div style="background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden;">
    <div style="padding:1.25rem 1.5rem; border-bottom:1px solid #F3F4F6; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size:.9rem; font-weight:700; color:#1A1F2E; margin:0;">
            Entrées d'audit
            <span style="font-size:.78rem; font-weight:400; color:#9CA3AF; margin-left:.5rem;">{{ $logs->total() }} entrée(s)</span>
        </h3>
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:.82rem;">
            <thead>
                <tr style="background:#F9FAFB; border-bottom:1px solid #F3F4F6;">
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em; white-space:nowrap;">Horodatage</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Utilisateur</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Action</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Entité</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">Détails</th>
                    <th style="text-align:left; padding:.75rem 1rem; color:#6B7280; font-weight:600; font-size:.72rem; text-transform:uppercase; letter-spacing:.05em;">IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr style="border-bottom:1px solid #F9FAFB;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background=''">
                    <td style="padding:.75rem 1rem; white-space:nowrap;">
                        <span style="font-family:'DM Mono', monospace; font-size:.76rem; color:#6B7280;">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </span>
                    </td>
                    <td style="padding:.75rem 1rem;">
                        @if($log->user)
                        <div style="display:flex; align-items:center; gap:.5rem;">
                            <div style="width:28px; height:28px; background:rgba(136,154,191,.2); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; color:#889ABF; flex-shrink:0;">
                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p style="font-size:.8rem; font-weight:600; color:#1A1F2E; margin:0;">{{ $log->user->name }}</p>
                                <p style="font-size:.72rem; color:#9CA3AF; margin:0;">{{ $log->user->role_label ?? $log->user->role }}</p>
                            </div>
                        </div>
                        @else
                        <span style="color:#9CA3AF; font-size:.78rem;">Système</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem;">
                        @php
                            $actionColors = [
                                'connexion' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
                                'deconnexion' => ['bg' => '#F3F4F6', 'text' => '#374151'],
                                'creation_ticket' => ['bg' => '#DBEAFE', 'text' => '#1D4ED8'],
                                'modification_ticket' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                                'affectation' => ['bg' => '#EDE9FE', 'text' => '#5B21B6'],
                                'resolution' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
                                'validation' => ['bg' => '#D1FAE5', 'text' => '#065F46'],
                                'creation_utilisateur' => ['bg' => '#DBEAFE', 'text' => '#1D4ED8'],
                                'modification_utilisateur' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
                            ];
                            $color = $actionColors[$log->action] ?? ['bg' => '#F3F4F6', 'text' => '#374151'];
                        @endphp
                        <span style="font-size:.72rem; font-weight:600; padding:.25rem .65rem; border-radius:20px; background:{{ $color['bg'] }}; color:{{ $color['text'] }}; white-space:nowrap;">
                            {{ str_replace('_', ' ', ucfirst($log->action)) }}
                        </span>
                    </td>
                    <td style="padding:.75rem 1rem;">
                        @if($log->entity_type && $log->entity_id)
                        <span style="font-family:'DM Mono', monospace; font-size:.76rem; color:#889ABF;">
                            {{ ucfirst($log->entity_type) }} #{{ $log->entity_id }}
                        </span>
                        @else
                        <span style="color:#9CA3AF; font-size:.76rem;">—</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem; max-width:280px;">
                        @if($log->details)
                            @php $details = is_array($log->details) ? $log->details : json_decode($log->details, true); @endphp
                            @if($details)
                            <button onclick="toggleDetails('log-{{ $log->id }}')"
                                style="font-size:.75rem; color:#889ABF; background:none; border:none; cursor:pointer; font-weight:600; padding:0; font-family:inherit;">
                                Voir les détails
                            </button>
                            <div id="log-{{ $log->id }}" style="display:none; margin-top:.4rem; background:#F9FAFB; border-radius:6px; padding:.6rem; font-size:.73rem; color:#4B5563; line-height:1.6; max-width:260px;">
                                @foreach($details as $key => $value)
                                    <div><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                @endforeach
                            </div>
                            @else
                            <span style="font-size:.78rem; color:#6B7280;">{{ $log->details }}</span>
                            @endif
                        @else
                        <span style="color:#9CA3AF; font-size:.76rem;">—</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem;">
                        <span style="font-family:'DM Mono', monospace; font-size:.74rem; color:#9CA3AF;">{{ $log->ip_address ?? '—' }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:4rem; color:#9CA3AF;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 1rem; display:block;"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M9 12h6M9 16h4"/></svg>
                        <p style="margin:0; font-size:.9rem; font-weight:600; color:#6B7280;">Aucune entrée d'audit</p>
                        <p style="margin:.25rem 0 0; font-size:.8rem;">Les actions des utilisateurs apparaîtront ici.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div style="padding:1rem 1.5rem; border-top:1px solid #F3F4F6;">
        {{ $logs->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<script>
function toggleDetails(id) {
    const el = document.getElementById(id);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>
@endsection
