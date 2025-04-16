<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
    protected $fillable = [
        'employe_id',
        'date_debut',
        'date_fin',
        'type',
        'statut',
        'duree',
        'solde_restant',
        'commentaire_manager',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'approved_at' => 'datetime'
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function approbateur()
    {
        return $this->belongsTo(Utilisateur::class, 'approved_by');
    }


    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
}