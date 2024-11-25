<?php

namespace App;

use App\Enums\OrdenCompraEstado;
use App\Helpers\SanitizerBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class Producto extends Model
{
    use SoftDeletes;
    protected $table = 'productos';
    protected $fillable = [
        'compra', 'venta', 'nombre', 'stock', 'categoria_id',
        'descripcion', 'codigo_barras', 'caducidad',
        'min_stock', 'max_stock'
    ];

    protected $hidden = ['compra'];

    protected $with = ['categoria', 'tags'];

    protected $appends = ['num_similares', 'num_proveedores', 'isGranel', 'unidades_paquete', 'pedidos'];

    private $_sanitizer;

    public function __construct(array $attrs = [])
    {
        parent::__construct($attrs);
        $this->_sanitizer = App::make(SanitizerBuilder::class);
    }

    public function ventas()
    {
        return $this->belongsToMany('ventas_productos', 'producto_id', 'venta_id');
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }
    public function perdida()
    {
        return $this->hasOne(Perdida::class, 'producto_id', 'id');
    }
    public function descuentos()
    {
        return $this->hasMany(Descuento::class, 'producto_id', 'id');
    }

    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'productos_proveedores',  'producto_id', 'proveedor_id')->withPivot(['codigo', 'disponible', 'last_check', 'default', 'precio']);
    }

    public function similares()
    {
        return $this->belongsToMany(Producto::class, 'productos_similares', 'base_producto_id', 'similar_producto_id');
    }

    public function garantiasDevoluciones()
    {
        return $this->hasMany(Producto::class, 'producto_id', 'id');
    }
    public function garantiasNuevo()
    {
        return $this->belongsToMany(Garantia::class, 'garantias_productos', 'garantia_id', 'producto_id')->withPivot(['cantidad']);
    }

    public function producto_granel()
    {
        return $this->hasOne(ProductoGranel::class, 'producto_id', 'id');
    }

    public function ordenesCompra()
    {
        return $this->belongsToMany(OrdenCompra::class, 'ordenes_compra_productos', 'producto_id', 'orden_compra_id')->withPivot([
            'compra',
            'cantidad'
        ]);
    }
    
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'tageable', 'tags_models');
    }

    // Assesors

    public function getNumSimilaresAttribute()
    {
        return $this->similares()->count();
    }

    public function getNumProveedoresAttribute()
    {
        return $this->proveedores()->count();
    }

    public function getMissingStockAttribute()
    {
        return $this->min_stock - ($this->stock + $this->pedidos);
    }

    public function getBestStockAttribute()
    {
        return $this->max_stock - ($this->stock + $this->pedidos);
    }

    public function getIsGranelAttribute()
    {
        return $this->producto_granel != null ? 1 : 0;
    }

    public function getUnidadesPaqueteAttribute()
    {
        return $this->producto_granel != null ? $this->producto_granel->unidades_paquete : 0;
    }

    public function getPedidosAttribute()
    {
        return $this->ordenesCompra()
            ->whereIn('estado', [OrdenCompraEstado::Pendiente->value, OrdenCompraEstado::Pedido->value])
            ->sum('cantidad');
    }

    private function sanitize($value): string
    {
        return $this->_sanitizer
            ->rmAcentos()
            ->trim()
            ->doUpperCase()
            ->apply($value);
    }

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = $this->sanitize($value);
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = $this->sanitize($value);
    }

    // // Scopes
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope('precios', function (Builder $builder) {
    //         $builder->where('productos.compra', '!=', -1)->where('productos.venta', '!=', -1);
    //     });
    // }
}
