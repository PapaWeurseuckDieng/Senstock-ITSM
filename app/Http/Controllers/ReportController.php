<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    private function checkAccess(): void
    {
        if (!auth()->user()->isITStaff()) {
            abort(403, 'Accès réservé à l\'équipe IT.');
        }
    }

    public function index()
    {
        $this->checkAccess();

        $tickets = Ticket::with(['user', 'assignee'])->get();

        $resolved      = $tickets->whereIn('status', ['resolu', 'ferme']);
        $total         = $tickets->count();
        $resolvedCount = $resolved->count();
        $slaOk         = $resolved->where('sla_breached', false)->count();
        $slaRate       = $resolvedCount > 0 ? round($slaOk / $resolvedCount * 100) : 0;

        $avgHours = $resolved
            ->filter(fn($t) => $t->t0_created_at && $t->t4_resolved_at)
            ->map(fn($t) => $t->t0_created_at->diffInHours($t->t4_resolved_at))
            ->avg();

        $stats = [
            'total'                => $total,
            'resolved'             => $resolvedCount,
            'in_progress'          => $tickets->where('status', 'en_cours')->count(),
            'open'                 => $tickets->where('status', 'ouvert')->count(),
            'sla_rate'             => $slaRate,
            'avg_resolution_hours' => $avgHours ? round($avgHours) : 0,
            'avg_satisfaction'     => round($resolved->whereNotNull('satisfaction_score')->avg('satisfaction_score') ?? 0, 1),
            'by_type'              => $tickets->groupBy('type')->map->count()->toArray(),
            'by_priority'          => $tickets->groupBy('priority')->map->count()->toArray(),
        ];

        $technicianStats = User::where('role', 'technicien')->get()->map(function ($tech) {
            $assigned        = Ticket::where('assigned_to', $tech->id)->get();
            $resolvedTickets = $assigned->whereIn('status', ['resolu', 'ferme']);
            $avgRes = $resolvedTickets
                ->filter(fn($t) => $t->t0_created_at && $t->t4_resolved_at)
                ->map(fn($t) => $t->t0_created_at->diffInHours($t->t4_resolved_at))
                ->avg();

            return [
                'name'                 => $tech->name,
                'total_assigned'       => $assigned->count(),
                'total_resolved'       => $resolvedTickets->count(),
                'sla_respected'        => $resolvedTickets->where('sla_breached', false)->count(),
                'avg_resolution_hours' => $avgRes ? round($avgRes) : null,
            ];
        });

        return view('reports.index', compact('stats', 'technicianStats'));
    }

    /**
     * Export CSV des tickets
     */
    /**
     * Export : redirige vers CSV ou PDF selon le paramètre format
     */
    public function export(Request $request)
    {
        $this->checkAccess();
        if ($request->format === 'pdf') {
            return $this->exportPdf($request);
        }
        return $this->exportCsv($request);
    }

    public function exportCsv(Request $request)
    {
        $this->checkAccess();
        $tickets = $this->getFilteredTickets($request);

        $filename = 'tickets_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($tickets) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'N° Ticket', 'Date création', 'Demandeur', 'Titre', 'Type',
                'Priorité', 'Statut', 'Catégorie', 'Assigné à',
                'Date assignation', 'Date résolution', 'SLA dépassé',
                'Score satisfaction', 'Note résolution',
            ], ';');

            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->ticket_number,
                    $ticket->t0_created_at?->format('d/m/Y H:i'),
                    $ticket->user->name ?? '',
                    $ticket->title,
                    $ticket->type_label,
                    $ticket->priority_label,
                    $ticket->status_label,
                    ucfirst($ticket->category ?? ''),
                    $ticket->assignee?->name ?? 'Non assigné',
                    $ticket->t1_assigned_at?->format('d/m/Y H:i'),
                    $ticket->t4_resolved_at?->format('d/m/Y H:i'),
                    $ticket->sla_breached ? 'Oui' : 'Non',
                    $ticket->satisfaction_score ?? '',
                    $ticket->resolution_note ?? '',
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export PDF du rapport
     */
    public function exportPdf(Request $request)
    {
        $this->checkAccess();

        $dateFrom = $request->date_from ? \Carbon\Carbon::parse($request->date_from) : now()->startOfMonth();
        $dateTo   = $request->date_to   ? \Carbon\Carbon::parse($request->date_to)   : now();

        $tickets = Ticket::with(['user', 'assignee'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->orderBy('created_at', 'desc')
            ->take(200)
            ->get();

        $resolved      = $tickets->whereIn('status', ['resolu', 'ferme']);
        $resolvedCount = $resolved->count();
        $slaOk         = $resolved->where('sla_breached', false)->count();
        $slaRate       = $resolvedCount > 0 ? round($slaOk / $resolvedCount * 100) : 0;

        $avgHours = $resolved
            ->filter(fn($t) => $t->t0_created_at && $t->t4_resolved_at)
            ->map(fn($t) => $t->t0_created_at->diffInHours($t->t4_resolved_at))
            ->avg();

        $stats = [
            'total'                => $tickets->count(),
            'resolved'             => $resolvedCount,
            'in_progress'          => $tickets->where('status', 'en_cours')->count(),
            'sla_rate'             => $slaRate,
            'avg_resolution_hours' => $avgHours ? round($avgHours) : 0,
            'avg_satisfaction'     => round($resolved->whereNotNull('satisfaction_score')->avg('satisfaction_score') ?? 0, 1),
        ];

        $technicianStats = User::where('role', 'technicien')->get()->map(function ($tech) use ($tickets) {
            $assigned        = $tickets->where('assigned_to', $tech->id);
            $resolvedTickets = $assigned->whereIn('status', ['resolu', 'ferme']);
            $avgRes = $resolvedTickets
                ->filter(fn($t) => $t->t0_created_at && $t->t4_resolved_at)
                ->map(fn($t) => $t->t0_created_at->diffInHours($t->t4_resolved_at))
                ->avg();

            return [
                'name'                 => $tech->name,
                'total_assigned'       => $assigned->count(),
                'total_resolved'       => $resolvedTickets->count(),
                'sla_respected'        => $resolvedTickets->where('sla_breached', false)->count(),
                'avg_resolution_hours' => $avgRes ? round($avgRes) : null,
            ];
        });

        $pdf = Pdf::loadView('reports.pdf', compact('tickets', 'stats', 'technicianStats', 'dateFrom', 'dateTo'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('rapport_itsm_' . now()->format('Y-m-d') . '.pdf');
    }

    private function getFilteredTickets(Request $request)
    {
        return Ticket::with(['user', 'assignee'])
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status,    fn($q) => $q->where('status', $request->status))
            ->when($request->priority,  fn($q) => $q->where('priority', $request->priority))
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
