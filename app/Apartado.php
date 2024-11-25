<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartado extends Model
{
    protected $table = 'apartados';

    protected $fillable = ['monto', 'concepto', 'usuario_id', 'apertura_caja_id'];

    protected $appends = ['created_at_formatted'];

    protected $with = ['usuario'];

    public function aperturaCaja()
    {
        return $this->belongsTo(AperturaCaja::class, 'apertura_caja_id', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id')->withTrashed();
    }

    public function setConceptoAttribute($value)
    {
        $this->attributes['concepto'] = mb_strtoupper($value);
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at?->format('Y-m-d H:i');
    }
}
