<?php

namespace App\Repositories;

use App\Classes\ISimilaresCombinator;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\StockLessThanZeroException;
use App\Helpers\CustomValidator;
use App\Helpers\LoggerBuilder;
use App\Producto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SimilaresRepository implements ISimilaresRepository
{
    private $_logger;
    private $_similaresCombinator;

    public function __construct(
        LoggerBuilder $logger,
        ISimilaresCombinator $similaresCombinator
    ) {
        $this->_logger = $logger;
        $this->_similaresCombinator = $similaresCombinator;
    }

    public function get($id)
    {
        try {
            return DB::table('productos_similares')
                ->join('productos', 'similar_producto_id', '=', 'id')
                ->where('base_producto_id', $id)
                ->where('productos.deleted_at')
                ->select('productos.codigo_barras', 'productos.nombre', 'productos.descripcion', 'productos.id', 'productos.stock')
                ->get();
        } catch (\Throwable $th) {

            $this->_logger
                ->error()
                ->description('Error al listar similares')
                ->exception($th)
                ->user_id(Auth::user()->id)
                ->link_id($id)
                ->log();
        }
    }

    public function create($productosIds)
    {
        try {
            DB::beginTransaction();

            $similares = $this->_similaresCombinator->combine($productosIds);

            foreach ($similares as $similitud) {

                DB::table('productos_similares')->insertOrIgnore([
                    'base_producto_id' => $similitud[0],
                    'similar_producto_id' => $similitud[1]
                ]);
            }

            DB::commit();

            $this->_logger
                ->success()
                ->description('Se agregó producto similar.')
                ->user_id(Auth::user()->id)
                ->link_id($productosIds[0])
                ->module($this::class)
                ->after(json_encode($productosIds))
                ->log();

            return true;
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Error al agregar producto similar.')
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->exception($th)
                ->before(json_encode($productosIds))
                ->log();

            return false;
        }
    }
}
