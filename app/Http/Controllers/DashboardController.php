<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isUtilisateur()) {
            return $this->userDashboard($user);
        }

        if ($user->isTechnicien()) {
            return $this->technicianDashboard($user);
        }

        return $this->managerDashboard($user);
    }

    // ─── Dashboard Utilisateur ───────────────────────────────────────────────

    private function userDashboard($user)
    {
        $stats = [
            'total'    => Ticket::forUser($user->id)->count(),
            'ouverts'  => Ticket::forUser($user->id)->where('status', 'ouvert')->count(),
            'en_cours' => Ticket::forUser($user->id)->where('status', 'en_cours')->count(),
            'resolus'  => Ticket::forUser($user->id)->whereIn('status', ['resolu', 'ferme'])->count(),
        ];

        $recentTickets = Ticket::forUser($user->id)
            ->with('assignee')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.user', compact('stats', 'recentTickets'));
    }

    // ─── Dashboard Technicien ────────────────────────────────────────────────

    private function technicianDashboard($user)
    {
        // Variables attendues par la vue dashboard/technician.blade.php
        $myOpenTickets = Ticket::assignedTo($user->id)
            ->where('status', 'ouvert')
            ->count();

        $myInProgressTickets = Ticket::assignedTo($user->id)
            ->where('status', 'en_cours')
            ->count();

        $myOverdueTickets = Ticket::assignedTo($user->id)
            ->overdue()
            ->count();

        $myResolvedThisMonth = Ticket::assignedTo($user->id)
            ->whereIn('status', ['resolu', 'ferme'])
            ->whereMonth('t4_resolved_at', now()->month)
            ->whereYear('t4_resolved_at', now()->year)
            ->count();

        $unassignedTickets = Ticket::whereNull('assigned_to')
            ->whereNotIn('status', ['ferme', 'annule'])
            ->count();

        // Tickets critiques/haute priorité assignés à ce technicien
        $urgentTickets = Ticket::assignedTo($user->id)
            ->whereIn('priority', ['critique', 'haute'])
            ->whereNotIn('status', ['ferme', 'annule', 'resolu'])
            ->with('user')
            ->orderByRaw("FIELD(priority, 'critique', 'haute')")
            ->orderBy('sla_deadline')
            ->limit(5)
            ->get();

        // Tickets non assignés à prendre en charge
        $pendingUnassigned = Ticket::whereNull('assigned_to')
            ->whereNotIn('status', ['ferme', 'annule'])
            ->with('user')
            ->orderByRaw("FIELD(priority, 'critique', 'haute', 'normale', 'faible')")
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        // Tous les tickets actifs du technicien
        $myActiveTickets = Ticket::assignedTo($user->id)
            ->whereNotIn('status', ['ferme', 'annule', 'resolu'])
            ->with('user')
            ->orderByRaw("FIELD(priority, 'critique', 'haute', 'normale', 'faible')")
            ->orderBy('sla_deadline')
            ->get();

        return view('dashboard.technician', compact(
            'myOpenTickets',
            'myInProgressTickets',
            'myOverdueTickets',
            'myResolvedThisMonth',
            'unassignedTickets',
            'urgentTickets',
            'pendingUnassigned',
            'myActiveTickets'
        ));
    }

    // ─── Dashboard Manager / Admin ───────────────────────────────────────────

    private function managerDashboard($user)
    {
        $stats = [
            'total'      => Ticket::count(),
            'ouverts'    => Ticket::where('status', 'ouvert')->count(),
            'en_cours'   => Ticket::where('status', 'en_cours')->count(),
            'resolus'    => Ticket::whereIn('status', ['resolu', 'ferme'])->count(),
            'overdue'    => Ticket::overdue()->count(),
            'sla_breached' => Ticket::where('sla_breached', true)->count(),
            'today'      => Ticket::whereDate('created_at', today())->count(),
            'this_week'  => Ticket::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        // Taux SLA
        $resolved          = Ticket::whereIn('status', ['resolu', 'ferme'])->count();
        $slaOk             = Ticket::whereIn('status', ['resolu', 'ferme'])->where('sla_breached', false)->count();
        $stats['sla_rate'] = $resolved > 0 ? round(($slaOk / $resolved) * 100, 1) : 100;

        // Délai moyen de résolution (heures)
        $avgResolution = Ticket::whereNotNull('t4_resolved_at')
            ->whereNotNull('t0_created_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, t0_created_at, t4_resolved_at)) as avg_minutes'))
            ->value('avg_minutes');
        $stats['avg_resolution_hours'] = $avgResolution ? round($avgResolution / 60, 1) : 0;

        // Satisfaction moyenne
        $stats['avg_satisfaction'] = round(
            Ticket::whereNotNull('satisfaction_score')->avg('satisfaction_score') ?? 0, 1
        );

        // Répartition par priorité et par type
        $byPriority = Ticket::select('priority', DB::raw('count(*) as total'))
            ->whereNotIn('status', ['ferme', 'annule'])
            ->groupBy('priority')
            ->pluck('total', 'priority');

        $byType = Ticket::select('type', DB::raw('count(*) as total'))
            ->whereNotIn('status', ['ferme', 'annule'])
            ->groupBy('type')
            ->pluck('total', 'type');

        // Tickets récents
        $recentTickets = Ticket::with(['user', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Performance techniciens
        $technicianPerf = User::where('role', 'technicien')
            ->withCount([
                'assignedTickets as total_assigned',
                'assignedTickets as resolved_count' => fn($q) => $q->whereIn('status', ['resolu', 'ferme']),
            ])
            ->get();

        // Activité récente (audit)
        $recentLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.manager', compact(
            'stats', 'byPriority', 'byType',
            'recentTickets', 'technicianPerf', 'recentLogs'
        ));
    }
}