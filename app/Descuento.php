<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Descuento extends Model
{
    use SoftDeletes;
    protected $table = "descuentos";
    protected $fillable = ["motivo", "producto_id", "usuario_id", "descuento", "nuevo", "tipo"];

    protected $with = ['usuario'];

    public function usuario()
    {
        return $this->belongsTo(User::class, "usuario_id", "id");
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, "producto_id", "id");
    }
    public function ventas()
    {
        return $this->belongsToMany(Venta::class, "ventas_descuentos", "descuento_id", "venta_id");
    }
}
