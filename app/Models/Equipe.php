<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipe extends Model
{
    protected $table = 'equipes';
    
    protected $fillable = [
        'manager_id',
        'employe_id',
        'date_affectation'
    ];

    public function manager()
    {
        return $this->belongsTo(Employe::class, 'manager_id');
    }

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'employe_id');
    }
}