<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotivoGarantia extends Model
{
    use SoftDeletes;
    protected $table = "motivos_garantias";

    protected $fillable = [
        'nombre'
    ];

    public function garantias()
    {
        return $this->hasMany(Garantia::class, "motivo_id", "id");
    }

    public function setNombreAttribute($value)
    {
        $this->attributes["nombre"] = mb_strtoupper($value);
    }
}
