<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoGrupo extends Model
{
    protected $table = 'catalogo_grupos';
    protected $fillable = ['nombre', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'grupo_id');
    }
}
