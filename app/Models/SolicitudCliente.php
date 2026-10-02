<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCliente extends Model
{
    protected $table = 'solicitudes_clientes';

    protected $fillable = [
        'user_id',
        'sucursal_id',
        'grupo_id',
        'color_id',
        'talle_id',
        'catalogo_item_id',
        'observacion',
        'estado',
        'revisado_por_id',
        'revisado_en',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'revisado_en' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function grupo()
    {
        return $this->belongsTo(CatalogoGrupo::class, 'grupo_id');
    }

    public function color()
    {
        return $this->belongsTo(CatalogoColor::class, 'color_id');
    }

    public function talle()
    {
        return $this->belongsTo(CatalogoTalle::class, 'talle_id');
    }

    public function item()
    {
        return $this->belongsTo(CatalogoItem::class, 'catalogo_item_id');
    }

    public function revisor()
    {
        return $this->belongsTo(User::class, 'revisado_por_id');
    }
}
