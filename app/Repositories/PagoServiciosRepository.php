<?php

namespace App\Repositories;

use App\Classes\ICierreAperturaCajaBuilder;
use App\Enums\Config;
use App\Helpers\LoggerBuilder;
use App\Recarga;
use App\Servicio;
use Exception;
use Illuminate\Support\Facades\DB;

class PagoServiciosRepository implements IPagoServiciosRepository
{
    private $_logger;
    private $_apertura;
    private IConfiguracionRepository $_configuracionRepository;

    public function __construct(
        LoggerBuilder $logger,
        IAperturasCajaRepository $apertura,
        IConfiguracionRepository $configuracionRepository
    ) {
        $this->_logger = $logger;
        $this->_apertura = $apertura;
        $this->_configuracionRepository = $configuracionRepository;
    }

    public function createRecarga($data)
    {
        try {
            DB::beginTransaction();

            Recarga::create([
                ...$data,
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

    public function createServicio($data)
    {
        try {
            DB::beginTransaction();
            $data['comision'] = $this->_configuracionRepository->get(Config::ComisionServicios);
            Servicio::create([
                ...$data,
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
