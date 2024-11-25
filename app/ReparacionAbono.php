<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReparacionAbono extends Model
{
    protected $table = "reparaciones_abonos";

    public function reparacion(){
        return $this->belongsTo(Reparacion::class, "reparacion_id", "id");
    }
}
