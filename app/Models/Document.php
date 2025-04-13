<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'employe_id',
        'nom',
        'chemin',
        'type',
        'date_upload',
        'date_expiration',
        'description'
    ];

    // Relations
    public function employe()
    {
        return $this->belongsTo(Employe::class);
    }
}