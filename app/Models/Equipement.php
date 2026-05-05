<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Equipement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'equipements';

    protected $fillable = [
        'code_inventaire', 'nom', 'categorie', 'marque', 'modele',
        'numero_serie', 'adresse_mac', 'adresse_ip',
        'assigned_user_id', 'localisation', 'departement', 'statut',
        'date_achat', 'prix_achat', 'fournisseur', 'numero_bon_commande',
        'fin_garantie', 'specifications', 'notes', 'created_by',
    ];

    protected $casts = [
        'date_achat'     => 'date',
        'fin_garantie'   => 'date',
        'prix_achat'     => 'decimal:2',
        'specifications' => 'array',
    ];

    // ─── Labels ──────────────────────────────────────────────────────────────

    const CATEGORIES = [
        'ordinateur_bureau'    => 'Ordinateur de bureau',
        'ordinateur_portable'  => 'Ordinateur portable',
        'serveur'              => 'Serveur',
        'imprimante'           => 'Imprimante',
        'switch'               => 'Switch réseau',
        'routeur'              => 'Routeur',
        'onduleur'             => 'Onduleur',
        'iot'                  => 'Objet IoT',
        'telephone_ip'         => 'Téléphone IP',
        'autre'                => 'Autre',
    ];

    const STATUTS = [
        'actif'            => 'Actif',
        'en_maintenance'   => 'En maintenance',
        'hors_service'     => 'Hors service',
        'en_stock'         => 'En stock',
        'retire'           => 'Retiré',
    ];

    const STATUT_COLORS = [
        'actif'          => ['bg' => '#D1FAE5', 'text' => '#065F46'],
        'en_maintenance' => ['bg' => '#FEF3C7', 'text' => '#92400E'],
        'hors_service'   => ['bg' => '#FEE2E2', 'text' => '#991B1B'],
        'en_stock'       => ['bg' => '#DBEAFE', 'text' => '#1E40AF'],
        'retire'         => ['bg' => '#F3F4F6', 'text' => '#374151'],
    ];

    const CATEGORIE_ICONS = [
        'ordinateur_bureau'   => '🖥️',
        'ordinateur_portable' => '💻',
        'serveur'             => '🖧',
        'imprimante'          => '🖨️',
        'switch'              => '🔀',
        'routeur'             => '📡',
        'onduleur'            => '🔋',
        'iot'                 => '📱',
        'telephone_ip'        => '☎️',
        'autre'               => '📦',
    ];

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->categorie] ?? ucfirst($this->categorie);
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut] ?? ucfirst($this->statut);
    }

    public function getStatutColorAttribute(): array
    {
        return self::STATUT_COLORS[$this->statut] ?? ['bg' => '#F3F4F6', 'text' => '#374151'];
    }

    public function getCategorieIconAttribute(): string
    {
        return self::CATEGORIE_ICONS[$this->categorie] ?? '📦';
    }

    public function getGarantieExpireedAttribute(): bool
    {
        return $this->fin_garantie && $this->fin_garantie->isPast();
    }

    public function getGarantieExpirationSoonAttribute(): bool
    {
        return $this->fin_garantie
            && !$this->fin_garantie->isPast()
            && $this->fin_garantie->diffInDays(now()) <= 30;
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function historique()
    {
        return $this->hasMany(EquipementHistorique::class)->orderBy('created_at', 'desc');
    }

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_equipement');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeGarantieExpirant($query, int $days = 30)
    {
        return $query->whereNotNull('fin_garantie')
                     ->where('fin_garantie', '>=', now())
                     ->where('fin_garantie', '<=', now()->addDays($days));
    }

    // ─── Boot : génération automatique du code inventaire ────────────────────

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipement) {
            if (empty($equipement->code_inventaire)) {
                $year  = now()->year;
                $count = static::whereYear('created_at', $year)->count() + 1;
                $equipement->code_inventaire = sprintf('EQ-%s-%04d', $year, $count);
            }
        });
    }
}
