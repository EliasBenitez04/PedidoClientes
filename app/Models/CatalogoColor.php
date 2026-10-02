<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoColor extends Model
{
    protected $table = 'color';
    protected $fillable = ['nombre', 'activo'];
    protected $casts = ['activo' => 'boolean'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'color_id');
    }
}
