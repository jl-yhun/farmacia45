<?php

namespace App\Repositories;

use App\Classes\ICierreAperturaCajaBuilder;
use App\Descuento;
use App\Helpers\LoggerBuilder;
use App\Venta;
use Exception;
use Illuminate\Support\Facades\DB;

class DescuentosRepository implements IDescuentosRepository
{
    private $_logger;
    public function __construct(LoggerBuilder $logger)
    {
        $this->_logger = $logger;
    }

    public function create($data)
    {
        try {
            $descuento = Descuento::create([
                ...$data,
                'usuario_id' => auth()->user()->id,
                'producto_id' => $data['id'],
            ]);

            $this->_logger
                ->success('agregar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->after(json_encode($data))
                ->log();
            return $descuento;
        } catch (Exception $e) {
            $this->_logger
                ->error('agregar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->before(json_encode($data))
                ->exception($e)
                ->log();
        }
        return false;
    }
}
