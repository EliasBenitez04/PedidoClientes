<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoColor extends Model
{
    protected $table = 'color';
    protected $fillable = ['nombre', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function solicitudes()
    {
        return $this->hasMany(SolicitudCliente::class, 'color_id');
    }
}
