<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'profile_id',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Um usuário pertence a um perfil
     * Relacionamento: belongsTo (N:1)
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    /**
     * Um usuário pode ter múltiplos endereços
     * Um endereço pode pertencer a múltiplos usuários
     * Relacionamento: belongsToMany (N:N)
     */
    public function addresses()
    {
        return $this->belongsToMany(Address::class, 'address_user')->withTimestamps();
    }
}