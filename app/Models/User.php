<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'rol',
        'sucursal_id',
        'activo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'ADMIN';
    }

    public function puedeVerTodo(): bool
    {
        return in_array($this->rol, ['ADMIN', 'SUPERVISOR'], true);
    }
}
