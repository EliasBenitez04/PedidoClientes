<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoItem extends Model
{
    protected $table = 'catalogo_items';
    protected $fillable = ['grupo', 'color', 'talle', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function pedidos() { return $this->hasMany(Pedido::class); }
}
