<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Um perfil pode ter múltiplos usuários
     * Relacionamento: hasMany (1:N)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}