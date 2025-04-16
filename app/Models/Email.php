<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'sujet',
        'message',
        'type',
        'from_user_id',
        'to_user_id',
        'is_read',
        'read_at'
    ];


    public function expediteur()
    {
        return $this->belongsTo(Utilisateur::class, 'from_user_id');
    }

    public function destinataire()
    {
        return $this->belongsTo(Utilisateur::class, 'to_user_id');
    }
}