<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TempsTravail extends Model
{
    protected $table = 'temps_travail';
    
    protected $fillable = [
        'employe_id',
        'date',
        'heure_debut',
        'heure_fin',
        'total_heures',
        'est_valide',
        'validated_by',
        'validated_at',
        'notes'
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function validateur()
    {
        return $this->belongsTo(Utilisateur::class, 'validated_by');
    }
}