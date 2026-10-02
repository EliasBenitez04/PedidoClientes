<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    protected $table = 'sucursales';
    protected $fillable = ['codigo', 'nombre', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    public function solicitudes()
    {
        return $this->hasMany(SolicitudCliente::class);
    }
}
