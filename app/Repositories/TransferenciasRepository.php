<?php

namespace App\Repositories;

use App\Classes\ICierreAperturaCajaBuilder;
use App\Helpers\LoggerBuilder;
use App\Recarga;
use App\Servicio;
use App\Transferencia;
use Exception;
use Illuminate\Support\Facades\DB;

class TransferenciasRepository implements ITransferenciasRepository
{
    private $_logger;
    private $_apertura;

    public function __construct(LoggerBuilder $logger, IAperturasCajaRepository $apertura)
    {
        $this->_logger = $logger;
        $this->_apertura = $apertura;
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();

            Transferencia::create([
                ...$data,
                'usuario_id' => auth()->user()->id,
                'apertura_caja_id' => $this->_apertura->getCurrent()->id
            ]);

            DB::commit();

            $this->_logger
                ->success('agregar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->after(json_encode($data))
                ->log();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error('agregar')
                ->user_id(auth()->user()->id)
                ->exception($e)
                ->before(json_encode($data))
                ->module($this::class)
                ->log();
        }
        return false;
    }
}
