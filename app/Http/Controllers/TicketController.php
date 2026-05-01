<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService) {}

    /**
     * Liste des tickets selon le rôle de l'utilisateur.
     */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Ticket::with(['user', 'assignee']);

        // Filtrer selon le rôle
        if ($user->isUtilisateur()) {
            $query->forUser($user->id);
        } elseif ($user->isTechnicien()) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)->orWhereNull('assigned_to');
            });
        }
        // Responsable IT et Admin voient tout

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Formulaire de création.
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Enregistrement du ticket.
     */
    public function store(StoreTicketRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $request->file('attachments');
        }

        $ticket = $this->ticketService->create($data, Auth::user());

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} créé avec succès. Le service IT a été notifié.");
    }

    /**
     * Détail d'un ticket.
     */
    public function show(Ticket $ticket)
    {
        $this->authorizeView($ticket);

        $ticket->load(['user', 'assignee', 'comments.user', 'attachments.user']);
        $technicians = User::where('role', 'technicien')->where('is_active', true)->get();
        $auditLogs   = AuditLog::where('model_type', Ticket::class)
                                ->where('model_id', $ticket->id)
                                ->with('user')
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('tickets.show', compact('ticket', 'technicians', 'auditLogs'));
    }

    /**
     * Assignation à un technicien.
     */
    public function assign(Request $request, Ticket $ticket)
    {
        $this->authorizeManage($ticket);

        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $technician = User::findOrFail($request->assigned_to);

        $this->ticketService->assign($ticket, $technician, Auth::user());

        return back()->with('success', "Ticket assigné à {$technician->name}.");
    }

    /**
     * Mise à jour du statut.
     */
    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorizeITStaff();

        $request->validate([
            'status' => 'required|in:en_cours,en_attente,resolu,ferme,annule',
        ]);

        $this->ticketService->updateStatus($ticket, $request->status, Auth::user());

        return back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Résolution du ticket.
     */
    public function resolve(Request $request, Ticket $ticket)
    {
        $this->authorizeITStaff();

        $request->validate(['resolution_note' => 'required|string|min:10']);

        $this->ticketService->resolve($ticket, $request->resolution_note, Auth::user());

        return back()->with('success', 'Ticket marqué comme résolu. L\'utilisateur a été notifié.');
    }

    /**
     * Validation de la résolution par l'utilisateur.
     */
    public function validateResolution(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Vous ne pouvez valider que vos propres tickets.');
        }

        $request->validate([
            'satisfaction_score'   => 'required|integer|min:1|max:5',
            'satisfaction_comment' => 'nullable|string|max:500',
        ]);

        $this->ticketService->validateResolution(
            $ticket,
            $request->satisfaction_score,
            $request->satisfaction_comment
        );

        return back()->with('success', 'Merci pour votre évaluation. Le ticket est maintenant fermé.');
    }

    /**
     * Ajouter un commentaire.
     */
    public function comment(Request $request, Ticket $ticket)
    {
        $this->authorizeView($ticket);

        $request->validate([
            'content'     => 'required|string|min:2|max:2000',
            'is_internal' => 'boolean',
        ]);

        $isInternal = $request->boolean('is_internal') && Auth::user()->isITStaff();

        $ticket->comments()->create([
            'user_id'     => Auth::id(),
            'content'     => $request->content,
            'is_internal' => $isInternal,
        ]);

        AuditLog::record('ticket.commented', $ticket, [], ['comment' => $request->content],
            'Commentaire ajouté par ' . Auth::user()->name);

        return back()->with('success', 'Commentaire ajouté.');
    }

    // ─── Helpers d'autorisation ──────────────────────────────────────────────

    /**
     * L'utilisateur peut voir ce ticket (il en est le demandeur, ou c'est un staff IT).
     */
    private function authorizeView(Ticket $ticket): void
    {
        $user = Auth::user();
        if ($user->isUtilisateur() && $ticket->user_id !== $user->id) {
            abort(403, 'Vous ne pouvez pas accéder à ce ticket.');
        }
    }

    /**
     * Seuls les techniciens, responsables IT et admins peuvent gérer (assigner) les tickets.
     */
    private function authorizeManage(Ticket $ticket): void
    {
        $user = Auth::user();
        if (!$user->isITStaff()) {
            abort(403, 'Action réservée à l\'équipe IT.');
        }
    }

    /**
     * Seuls les membres de l'équipe IT peuvent modifier le statut ou résoudre.
     */
    private function authorizeITStaff(): void
    {
        if (!Auth::user()->isITStaff()) {
            abort(403, 'Action réservée à l\'équipe IT.');
        }
    }
}