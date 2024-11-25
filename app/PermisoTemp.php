<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PermisoTemp extends Model
{
    protected $table = "permisos_temp";
    protected $fillable = ["permiso_id", "usuario_id", "expiracion", "razon"];
}
