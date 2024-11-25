<?php

namespace App;

use App\Helpers\SanitizerBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Categoria extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = "categorias";

    protected $fillable = [
        'nombre', 'admite', 'tasa_iva'
    ];

    protected $appends = ['tasa_iva_formatted', 'is_destroyable'];

    private $_sanitizer;

    public function __construct()
    {
        parent::__construct();
        $this->_sanitizer = App::make(SanitizerBuilder::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class, "categoria_id", "id");
    }

    public function getTasaIvaFormattedAttribute()
    {
        return $this->tasa_iva * 100 . '%';
    }

    public function getIsDestroyableAttribute()
    {
        return $this->productos()->count() == 0;
    }

    public function setNombreAttribute($value)
    {
        $value = $this->_sanitizer
            ->rmAcentos()
            ->trim()
            ->doUpperCase()
            ->apply($value);

        $this->attributes["nombre"] = $value;
    }
}
