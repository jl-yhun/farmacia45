<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    protected $table = 'transferencias';

    protected $fillable = ['monto', 'concepto', 'tipo', 'usuario_id', 'apertura_caja_id'];
}
