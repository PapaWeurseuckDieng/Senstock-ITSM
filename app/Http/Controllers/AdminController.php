<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private function checkAccess(): void
    {
        if (!auth()->user()->isAdministrateur() && !auth()->user()->isResponsableIT()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }
    }

    // ─── Gestion des utilisateurs ─────────────────────────────────────────────

    public function users()
    {
        $this->checkAccess();
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $this->checkAccess();
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $this->checkAccess();
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users',
            'password'   => 'required|min:8|confirmed',
            'role'       => 'required|in:utilisateur,technicien,responsable_it,administrateur',
            'phone'      => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        AuditLog::record('user.created', $user, [], $user->toArray(), "Utilisateur {$user->name} créé");

        return redirect()->route('admin.users')->with('success', "Utilisateur {$user->name} créé avec succès.");
    }

    public function editUser(User $user)
    {
        $this->checkAccess();
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $this->checkAccess();
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'role'       => 'required|in:utilisateur,technicien,responsable_it,administrateur',
            'phone'      => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'is_active'  => 'boolean',
        ]);

        $old = $user->toArray();
        $user->update($data);

        AuditLog::record('user.updated', $user, $old, $user->fresh()->toArray(),
            "Utilisateur {$user->name} mis à jour");

        return redirect()->route('admin.users.index')->with('success', "Utilisateur mis à jour.");
    }

    public function toggleUserStatus(User $user)
    {
        $this->checkAccess();
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activé' : 'désactivé';

        AuditLog::record("user.{$status}", $user, [], $user->fresh()->toArray(),
            "Compte de {$user->name} {$status}");

        return back()->with('success', "Compte {$status}.");
    }

    // ─── Logs d'audit ────────────────────────────────────────────────────────

    public function auditLogs(Request $request)
    {
        $this->checkAccess();
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->action . '%');
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs  = $query->paginate(30)->withQueryString();
        $users = User::orderBy('name')->get();

        return view('admin.audit', compact('logs', 'users'));
    }
}
