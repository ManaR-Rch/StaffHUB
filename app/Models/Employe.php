<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employe extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'utilisateur_id',
        'date_naissance',
        'poste',
        'departement',
        'date_embauche',
        'statut',
        'numero_employe',
        'solde_conge'
    ];

    protected $dates = ['deleted_at'];

    // Relations
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }

    public function conges()
    {
        return $this->hasMany(Conge::class);
    }

    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function tempsTravail()
    {
        return $this->hasMany(TempsTravail::class);
    }

    public function paies()
    {
        return $this->hasMany(Paie::class);
    }

    public function taches()
    {
        return $this->hasMany(Tache::class, 'assigned_to');
    }

    public function equipe()
    {
        return $this->belongsToMany(Employe::class, 'equipes', 'employe_id', 'manager_id')
                   ->withTimestamps();
    }
}