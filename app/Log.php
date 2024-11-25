<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = "log";

    protected $fillable = ["link_id", "modulo", "tipo", "descripcion", "antes", "despues", "usuario_id", "excepcion"];
}
