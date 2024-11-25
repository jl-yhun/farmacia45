<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use SoftDeletes;
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre'
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'productos_proveedores', 'producto_id', 'proveedor_id')->withPivot(['codigo', 'disponible', 'last_check', 'default', 'precio']);
    }
}
