<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tache extends Model
{
    protected $fillable = [
        'titre',
        'description',
        'statut',
        'date_echeance',
        'created_by',
        'assigned_to',
        'priorite',
        'notes'
    ];

    protected $dates = ['date_echeance'];

    public function createur()
    {
        return $this->belongsTo(Utilisateur::class, 'created_by');
    }

    public function assigneA()
    {
        return $this->belongsTo(Employe::class, 'assigned_to');
    }
}