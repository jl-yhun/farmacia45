<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perdida extends Model
{
    use SoftDeletes;
    protected $table = "perdidas";
    protected $fillable = ["usuario_id", "producto_id", "garantia_id", "motivo"];

    public function usuario()
    {
        return $this->belongsTo(User::class, "usuario_id", "id");
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, "producto_id", "id")->withTrashed();
    }
    public function garantia()
    {
        return $this->belongsTo(Garantia::class, "garantia_id", "id");
    }

    public function setMotivoAttribute($value)
    {
        $this->attributes["motivo"] = mb_strtoupper($value);
    }
}
