<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Garantia extends Model
{
    use SoftDeletes;
    protected $table = "garantias";

    protected $with = ["producto_devuelto", "venta", "productos_nuevos", "usuario"];

    protected $fillable = ["usuario_id", "producto_id", "venta_id", "tipo", "observaciones", "diferencia", "perdida", "apertura_caja_id"];

    public function aperturaCaja()
    {
        return $this->belongsTo(AperturaCaja::class, "apertura_caja_id", "id");
    }
    public function producto_devuelto()
    {
        return $this->belongsTo(Producto::class, "producto_id", "id")->withTrashed();
    }
    public function productos_nuevos()
    {
        return $this->belongsToMany(Producto::class, "garantias_productos", "garantia_id", "producto_id")->withTrashed()->withPivot(["cantidad"]);
    }
    public function perdida()
    {
        return $this->hasOne(Perdida::class, "garantia_id", "id");
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, "usuario_id", "id");
    }
    public function venta()
    {
        return $this->belongsTo(Venta::class, "venta_id", "id");
    }
    public function getDiferenciaStrAttribute()
    {
        return mb_strtoupper($this->diferencia < 0 ? 'El cliente pagó una diferencia de $' . $this->diferencia * -1 : ($this->diferencia == 0 ? "NA" : "Saldo a favor de $ $this->diferencia"));
    }
}
