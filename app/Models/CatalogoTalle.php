<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoTalle extends Model
{
    protected $table = 'catalogo_talles';
    protected $fillable = ['nombre', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'talle_id');
    }
}
