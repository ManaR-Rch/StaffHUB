<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conge extends Model
{
  

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'approved_at' => 'datetime'
    ];

    // Relations
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }

    public function approbateur()
    {
        return $this->belongsTo(Utilisateur::class, 'approved_by');
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }
}