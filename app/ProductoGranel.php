<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoGranel extends Model
{
    protected $table = 'productos_granel';
    protected $fillable = ['producto_id', 'unidades_paquete'];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
