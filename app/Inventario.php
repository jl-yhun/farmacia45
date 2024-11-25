<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventarios';

    protected $fillable = ['producto_id', 'cantidad', 'compra', 'venta', 'procesado'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id')->select(['id', 'codigo_barras', 'nombre', 'descripcion']);
    }

    protected static function booted()
    {
        static::addGlobalScope('noProceseed', function (Builder $builder) {
            $builder->where('procesado', 0);
        });
    }
}
