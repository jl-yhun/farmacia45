<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recarga extends Model
{
    protected $table = 'recargas';

    protected $fillable = ['monto', 'compania', 'apertura_caja_id', 'metodo_pago'];
}
