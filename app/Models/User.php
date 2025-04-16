<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'utilisateurs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nom',
        'prenom', 
        'email',
        'mot_de_passe',
        'role'
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token'
    ];

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}