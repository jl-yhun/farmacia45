<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;

class Reparacion extends Model
{
    protected $table = "reparaciones";

    public function abonos(){
        return $this->hasMany(ReparacionAbono::class, "reparacion_id", "id");
    }

    public function getEntregaAttribute(){
         return new Date($this->fecha_entrega);
    }

    protected $casts = [
        "fecha_entrega" => "date"
    ];
}
