<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenCompra extends Model
{
    use SoftDeletes;
    protected $table = 'ordenes_compra';

    protected $with = ['proveedor', 'creador', 'recibidor', 'aplicador', 'productos'];

    protected $fillable = [
        'estado', 'proveedor_id',
        'creador_id', 'recibidor_id', 'aplicador_id',
        'apertura_caja_id', 'total'
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'ordenes_compra_productos', 'orden_compra_id', 'producto_id')->withPivot([
            'compra',
            'cantidad'
        ]);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creador_id', 'id');
    }

    public function recibidor()
    {
        return $this->belongsTo(User::class, 'recibidor_id', 'id')->withTrashed();
    }

    public function aplicador()
    {
        return $this->belongsTo(User::class, 'aplicador_id', 'id')->withTrashed();
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class)->withTrashed();
    }

    public function tags()
    {
        return $this->morphedByMany(Tag::class, 'tagebale', 'tags_models');
    }
}
