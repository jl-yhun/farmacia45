<?php

namespace App;

use App\Enums\TipoGarantia;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Venta extends Model
{
    use SoftDeletes;

    protected $table = "ventas";

    protected $with = ["usuario", "productos", "descuentos"];

    protected $appends = ["created_at_formated"];

    protected $fillable = ['usuario_id', 'apertura_caja_id', 'metodo_pago', 'total', 'denominacion', 'cambio', 'utilidad'];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, "ventas_productos", "venta_id", "producto_id")->withTrashed()->withPivot([
            "cantidad",
            "compra",
            "venta"
        ]);
    }
    public function descuentos()
    {
        return $this->belongsToMany(Descuento::class, "ventas_descuentos", "descuento_id", "venta_id");
    }
    
    public function aperturaCaja()
    {
        return $this->belongsTo(AperturaCaja::class, "apertura_caja_id", "id");
    }
    public function usuario()
    {
        return $this->belongsTo(User::class, "usuario_id", "id")->withTrashed();
    }

    public function garantias()
    {
        return $this->hasMany(Garantia::class, "venta_id", "id");
    }

    public function getHasGarantiaAttribute()
    {
        return date_diff($this->created_at, new DateTime())->format("%d") <= _c("DIAS_GARANTIA");
    }

    public function getHasCancelacionesAttribute()
    {
        return $this->garantias()
            ->where('tipo', TipoGarantia::Cancelacion)
            ->where('apertura_caja_id', getAperturaCajaIfExist())->count() > 0;
    }

    public function getProductosReclamadosAttribute()
    {
        $productosReclamados = $this->garantias()->get()->map(function ($garantia) {
            return $garantia->producto_devuelto;
        })->pluck('id')->all();
        return $productosReclamados;
    }
    public function getGarantiaAplicadaAttribute()
    {
        return $this->productos()
            ->whereNotIn(DB::raw('productos.id'), $this->getProductosReclamadosAttribute())
            ->count() == 0;
    }
    public function getCreatedAtFormatedAttribute()
    {
        return $this->created_at->format("Y-m-d H:i");
    }
}
