<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AperturaCaja extends Model
{
    protected $table = 'aperturas_caja';

    protected $fillable = [
        'inicial_efe', 'inicial_ele',
        'inicial_apartados', 'inicial_recargas_servicios',
        'ventas_efe', 'ventas_ele',
        'utilidades', 'total',
        'subtotal_efe', 'subtotal_ele',
        'subtotal_apartados', 'subtotal_recargas_servicios',
        'gastos_efe', 'gastos_ele',
        'gastos_apartados', 'gastos_recargas_servicios',
        'servicios_recargas_efe',
        'servicios_recargas_ele',
        'apartados_dia',
        'fecha_hora_cierre', 'estado',
        'observaciones'
    ];

    protected $appends = ['created_at_formatted'];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'apertura_caja_id', 'id');
    }

    public function garantias()
    {
        return $this->hasMany(Garantia::class, 'apertura_caja_id', 'id');
    }

    public function recargas()
    {
        return $this->hasMany(Recarga::class, 'apertura_caja_id', 'id');
    }

    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'apertura_caja_id', 'id');
    }

    public function transferencias()
    {
        return $this->hasMany(Transferencia::class, 'apertura_caja_id', 'id');
    }

    public function gastos()
    {
        return $this->hasMany(Gasto::class, 'apertura_caja_id', 'id');
    }

    public function apartados()
    {
        return $this->hasMany(Apartado::class, 'apertura_caja_id', 'id');
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('Y-m-d');
    }
}
