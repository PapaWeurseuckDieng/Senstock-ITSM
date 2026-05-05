<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipementHistorique extends Model
{
    public $timestamps = false;

    protected $table = 'equipement_historique';

    protected $fillable = [
        'equipement_id', 'user_id', 'type_mouvement',
        'ancienne_valeur', 'nouvelle_valeur', 'commentaire', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    const TYPES = [
        'affectation'              => 'Affectation',
        'desaffectation'           => 'Désaffectation',
        'maintenance'              => 'Mise en maintenance',
        'retour_stock'             => 'Retour en stock',
        'mise_hors_service'        => 'Mise hors service',
        'changement_localisation'  => 'Changement de localisation',
        'autre'                    => 'Autre',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type_mouvement] ?? ucfirst($this->type_mouvement);
    }

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper pour enregistrer un mouvement
    public static function enregistrer(
        Equipement $equipement,
        string $type,
        ?string $ancienne = null,
        ?string $nouvelle = null,
        ?string $commentaire = null
    ): self {
        return static::create([
            'equipement_id'   => $equipement->id,
            'user_id'         => auth()->id(),
            'type_mouvement'  => $type,
            'ancienne_valeur' => $ancienne,
            'nouvelle_valeur' => $nouvelle,
            'commentaire'     => $commentaire,
            'created_at'      => now(),
        ]);
    }
}
