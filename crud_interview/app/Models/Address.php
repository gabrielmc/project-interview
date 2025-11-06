<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
    ];

    /**
     * Um endereço pode pertencer a múltiplos usuários
     * Relacionamento: belongsToMany (N:N)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'address_user')->withTimestamps();
    }
}