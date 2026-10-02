<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoGrupo extends Model
{
    protected $table = 'grupo';
    protected $fillable = ['nombre', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function solicitudes()
    {
        return $this->hasMany(SolicitudCliente::class, 'grupo_id');
    }
}
