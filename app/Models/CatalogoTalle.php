<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoTalle extends Model
{
    protected $table = 'talle';
    protected $fillable = ['nombre', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function solicitudes()
    {
        return $this->hasMany(SolicitudCliente::class, 'talle_id');
    }
}
