<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gasto extends Model
{
    use SoftDeletes;
    protected $table = 'gastos';
    protected $fillable = [
        'usuario_id',
        'concepto', 'monto', 'apertura_caja_id',
        'fuente'
    ];

    public function aperturaCaja()
    {
        return $this->belongsTo(AperturaCaja::class, 'apertura_caja_id', 'id');
    }
    public function setConceptoAttribute($value)
    {
        $this->attributes['concepto'] = mb_strtoupper($value);
    }
}
