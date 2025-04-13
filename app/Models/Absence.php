<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'employe_id',
        'date_debut',
        'date_fin',
        'raison',
        'justificatif',
        'est_justifie',
        'commentaire'
    ];

    // Relations
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}