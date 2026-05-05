<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Equipement;
use App\Models\EquipementHistorique;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ParcController extends Controller
{
    private function checkAccess(): void
    {
        if (!Auth::user()->isITStaff()) {
            abort(403, 'Accès réservé à l\'équipe IT.');
        }
    }

    // ─── Liste des équipements ────────────────────────────────────────────────

    public function index(Request $request)
    {
        $this->checkAccess();

        $query = Equipement::with('assignedUser');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                  ->orWhere('code_inventaire', 'like', "%{$s}%")
                  ->orWhere('marque', 'like', "%{$s}%")
                  ->orWhere('modele', 'like', "%{$s}%")
                  ->orWhere('numero_serie', 'like', "%{$s}%")
                  ->orWhere('adresse_ip', 'like', "%{$s}%");
            });
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('departement')) {
            $query->where('departement', 'like', '%' . $request->departement . '%');
        }

        $equipements = $query->orderBy('code_inventaire')->paginate(20)->withQueryString();

        // KPI pour l'en-tête
        $kpi = [
            'total'           => Equipement::count(),
            'actifs'          => Equipement::where('statut', 'actif')->count(),
            'en_maintenance'  => Equipement::where('statut', 'en_maintenance')->count(),
            'garantie_expire' => Equipement::whereNotNull('fin_garantie')
                                           ->where('fin_garantie', '<', now())->count(),
        ];

        // Stats par catégorie
        $parCategorie = Equipement::selectRaw('categorie, count(*) as total')
            ->groupBy('categorie')
            ->pluck('total', 'categorie');

        return view('parc.index', compact('equipements', 'kpi', 'parCategorie'));
    }

    // ─── Formulaire de création ───────────────────────────────────────────────

    public function create()
    {
        $this->checkAccess();
        $utilisateurs = User::where('is_active', true)->orderBy('name')->get();
        return view('parc.create', compact('utilisateurs'));
    }

    // ─── Enregistrement ──────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $this->checkAccess();

        $data = $request->validate([
            'nom'                  => 'required|string|max:255',
            'categorie'            => 'required|in:' . implode(',', array_keys(Equipement::CATEGORIES)),
            'marque'               => 'nullable|string|max:100',
            'modele'               => 'nullable|string|max:150',
            'numero_serie'         => 'nullable|string|max:100|unique:equipements,numero_serie',
            'adresse_mac'          => 'nullable|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            'adresse_ip'           => 'nullable|ip',
            'assigned_user_id'     => 'nullable|exists:users,id',
            'localisation'         => 'nullable|string|max:255',
            'departement'          => 'nullable|string|max:150',
            'statut'               => 'required|in:' . implode(',', array_keys(Equipement::STATUTS)),
            'date_achat'           => 'nullable|date',
            'prix_achat'           => 'nullable|numeric|min:0',
            'fournisseur'          => 'nullable|string|max:150',
            'numero_bon_commande'  => 'nullable|string|max:100',
            'fin_garantie'         => 'nullable|date|after_or_equal:date_achat',
            'spec_cpu'             => 'nullable|string|max:100',
            'spec_ram'             => 'nullable|string|max:50',
            'spec_stockage'        => 'nullable|string|max:100',
            'spec_os'              => 'nullable|string|max:100',
            'notes'                => 'nullable|string|max:2000',
        ]);

        // Construire les specs
        $specs = array_filter([
            'cpu'      => $request->spec_cpu,
            'ram'      => $request->spec_ram,
            'stockage' => $request->spec_stockage,
            'os'       => $request->spec_os,
        ]);
        $data['specifications'] = !empty($specs) ? $specs : null;
        $data['created_by']     = Auth::id();

        $equipement = Equipement::create($data);

        // Historique initial
        EquipementHistorique::enregistrer(
            $equipement, 'autre', null, 'Créé',
            'Ajout au parc informatique par ' . Auth::user()->name
        );

        // Si affecté directement à un utilisateur
        if ($equipement->assigned_user_id) {
            EquipementHistorique::enregistrer(
                $equipement, 'affectation', null,
                $equipement->assignedUser->name,
                'Affectation initiale'
            );
        }

        AuditLog::record('equipement.created', $equipement, [], $data,
            'Équipement ' . $equipement->code_inventaire . ' ajouté au parc');

        return redirect()
            ->route('parc.show', $equipement)
            ->with('success', "Équipement {$equipement->code_inventaire} ajouté avec succès.");
    }

    // ─── Détail d'un équipement ───────────────────────────────────────────────

    public function show(Equipement $equipement)
    {
        $this->checkAccess();

        $equipement->load(['assignedUser', 'historique.user', 'tickets']);
        $utilisateurs = User::where('is_active', true)->orderBy('name')->get();

        return view('parc.show', compact('equipement', 'utilisateurs'));
    }

    // ─── Formulaire d'édition ─────────────────────────────────────────────────

    public function edit(Equipement $equipement)
    {
        $this->checkAccess();
        $utilisateurs = User::where('is_active', true)->orderBy('name')->get();
        return view('parc.edit', compact('equipement', 'utilisateurs'));
    }

    // ─── Mise à jour ─────────────────────────────────────────────────────────

    public function update(Request $request, Equipement $equipement)
    {
        $this->checkAccess();

        $data = $request->validate([
            'nom'                  => 'required|string|max:255',
            'categorie'            => 'required|in:' . implode(',', array_keys(Equipement::CATEGORIES)),
            'marque'               => 'nullable|string|max:100',
            'modele'               => 'nullable|string|max:150',
            'numero_serie'         => 'nullable|string|max:100|unique:equipements,numero_serie,' . $equipement->id,
            'adresse_mac'          => 'nullable|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
            'adresse_ip'           => 'nullable|ip',
            'assigned_user_id'     => 'nullable|exists:users,id',
            'localisation'         => 'nullable|string|max:255',
            'departement'          => 'nullable|string|max:150',
            'statut'               => 'required|in:' . implode(',', array_keys(Equipement::STATUTS)),
            'date_achat'           => 'nullable|date',
            'prix_achat'           => 'nullable|numeric|min:0',
            'fournisseur'          => 'nullable|string|max:150',
            'numero_bon_commande'  => 'nullable|string|max:100',
            'fin_garantie'         => 'nullable|date',
            'spec_cpu'             => 'nullable|string|max:100',
            'spec_ram'             => 'nullable|string|max:50',
            'spec_stockage'        => 'nullable|string|max:100',
            'spec_os'              => 'nullable|string|max:100',
            'notes'                => 'nullable|string|max:2000',
        ]);

        $old = $equipement->toArray();

        // Historique : changement de statut
        if ($data['statut'] !== $equipement->statut) {
            $typeMap = [
                'en_maintenance' => 'maintenance',
                'hors_service'   => 'mise_hors_service',
                'en_stock'       => 'retour_stock',
            ];
            EquipementHistorique::enregistrer(
                $equipement,
                $typeMap[$data['statut']] ?? 'autre',
                $equipement->statut_label,
                Equipement::STATUTS[$data['statut']] ?? $data['statut']
            );
        }

        // Historique : changement d'affectation
        $oldUserId = $equipement->assigned_user_id;
        $newUserId = $data['assigned_user_id'] ?? null;
        if ($oldUserId !== $newUserId) {
            $oldUser = $oldUserId ? User::find($oldUserId)?->name : 'Non affecté';
            $newUser = $newUserId ? User::find($newUserId)?->name : 'Non affecté';
            EquipementHistorique::enregistrer(
                $equipement,
                $newUserId ? 'affectation' : 'desaffectation',
                $oldUser, $newUser
            );
        }

        // Historique : changement de localisation
        if (($data['localisation'] ?? null) !== $equipement->localisation) {
            EquipementHistorique::enregistrer(
                $equipement, 'changement_localisation',
                $equipement->localisation ?? '—',
                $data['localisation'] ?? '—'
            );
        }

        // Specs
        $specs = array_filter([
            'cpu'      => $request->spec_cpu,
            'ram'      => $request->spec_ram,
            'stockage' => $request->spec_stockage,
            'os'       => $request->spec_os,
        ]);
        $data['specifications'] = !empty($specs) ? $specs : null;

        $equipement->update($data);

        AuditLog::record('equipement.updated', $equipement, $old, $data,
            'Équipement ' . $equipement->code_inventaire . ' mis à jour');

        return redirect()
            ->route('parc.show', $equipement)
            ->with('success', 'Équipement mis à jour avec succès.');
    }



    // ─── Export CSV du parc ───────────────────────────────────────────────────

    public function exportCsv()
    {
        $this->checkAccess();

        $equipements = Equipement::with('assignedUser')->orderBy('code_inventaire')->get();

        $filename = 'parc_informatique_' . now()->format('Y-m-d') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($equipements) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Code inventaire', 'Nom', 'Categorie', 'Marque', 'Modele',
                'N Serie', 'Adresse MAC', 'Adresse IP',
                'Statut', 'Affecte a', 'Localisation', 'Departement',
                'Date achat', 'Prix achat (FCFA)', 'Fournisseur', 'N Bon commande',
                'Fin garantie', 'CPU', 'RAM', 'Stockage', 'OS', 'Notes',
            ], ';');

            foreach ($equipements as $eq) {
                $specs = $eq->specifications ?? [];
                fputcsv($handle, [
                    $eq->code_inventaire,
                    $eq->nom,
                    $eq->category_label,
                    $eq->marque ?? '',
                    $eq->modele ?? '',
                    $eq->numero_serie ?? '',
                    $eq->adresse_mac ?? '',
                    $eq->adresse_ip ?? '',
                    $eq->statut_label,
                    $eq->assignedUser?->name ?? 'Non affecte',
                    $eq->localisation ?? '',
                    $eq->departement ?? '',
                    $eq->date_achat?->format('d/m/Y') ?? '',
                    $eq->prix_achat ?? '',
                    $eq->fournisseur ?? '',
                    $eq->numero_bon_commande ?? '',
                    $eq->fin_garantie?->format('d/m/Y') ?? '',
                    $specs['cpu'] ?? '',
                    $specs['ram'] ?? '',
                    $specs['stockage'] ?? '',
                    $specs['os'] ?? '',
                    $eq->notes ?? '',
                ], ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── Export PDF du parc ───────────────────────────────────────────────────

    public function exportPdf()
    {
        $this->checkAccess();

        $equipements     = Equipement::with('assignedUser')->orderBy('code_inventaire')->get();
        $generatedAt     = now();

        $kpi = [
            'total'          => $equipements->count(),
            'actifs'         => $equipements->where('statut', 'actif')->count(),
            'en_maintenance' => $equipements->where('statut', 'en_maintenance')->count(),
            'hors_service'   => $equipements->where('statut', 'hors_service')->count(),
            'garantie_expire'=> $equipements->filter(fn($e) => $e->fin_garantie && $e->fin_garantie->isPast())->count(),
        ];

        $pdf = Pdf::loadView('parc.pdf', compact('equipements', 'kpi', 'generatedAt'))
            ->setPaper('A4', 'landscape');

        return $pdf->download('parc_informatique_' . now()->format('Y-m-d') . '.pdf');
    }

    // ─── Suppression (soft delete) ────────────────────────────────────────────

    public function destroy(Equipement $equipement)
    {
        $this->checkAccess();

        // Seuls admin et responsable IT peuvent supprimer
        if (!Auth::user()->isAdministrateur() && !Auth::user()->isResponsableIT()) {
            abort(403, 'Suppression réservée aux administrateurs.');
        }

        AuditLog::record('equipement.deleted', $equipement, $equipement->toArray(), [],
            'Équipement ' . $equipement->code_inventaire . ' retiré du parc');

        $equipement->delete();

        return redirect()
            ->route('parc.index')
            ->with('success', 'Équipement retiré du parc informatique.');
    }
}
