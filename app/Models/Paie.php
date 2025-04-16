<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paie extends Model
{
    protected $fillable = [
        'employe_id',
        'mois',
        'salaire_base',
        'primes',
        'deductions',
        'salaire_net',
        'statut',
        'fichier_pdf',
        'notes',
        'created_by',
        'validated_by',
        'validated_at'
    ];


    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }

    public function validateur()
    {
        return $this->belongsTo(Utilisateur::class, 'validated_by');
    }
}