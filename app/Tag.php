<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';

    protected $fillable = ['usuario_id', 'nombre'];

    public function products()
    {
        return $this->morphedByMany(Producto::class, 'tageable');
    }

    public function ordenesCompra()
    {
        return $this->morphedByMany(OrdenCompra::class, 'tageable');
    }
}
