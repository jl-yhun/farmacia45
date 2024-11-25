<?php

namespace App\Repositories;

use App\AperturaCaja;
use App\Classes\ICierreAperturaCajaBuilder;
use App\Descuento;
use App\Enums\AperturaCajaEstado;
use App\Enums\MetodoPago;
use App\Enums\VentaEstado;
use App\Helpers\LoggerBuilder;
use App\Producto;
use App\Venta;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PuntoVentaRepository implements IPuntoVentaRepository
{
    private $_configuracionRepository;
    private $_logger;
    private $_aperturaRepository;
    private $_cierreCajaBuilder;
    private $_productosRepository;
    private $_ventasRepository;

    public function __construct(
        IConfiguracionRepository $configuracionRepository,
        LoggerBuilder $logger,
        IAperturasCajaRepository $aperturaRepository,
        IProductosRepository $productosRepository,
        IVentasRepository $ventasRepository,
        ICierreAperturaCajaBuilder $cierreCajaBuilder
    ) {
        $this->_configuracionRepository = $configuracionRepository;
        $this->_logger = $logger;
        $this->_aperturaRepository = $aperturaRepository;
        $this->_productosRepository = $productosRepository;
        $this->_ventasRepository = $ventasRepository;
        $this->_cierreCajaBuilder = $cierreCajaBuilder;
    }

    public function existsAperturaCaja()
    {
        // TODO: Move this code to CierreCajaBuilder
        return AperturaCaja::where('estado', 'Pendiente')->count() > 0;
    }

    public function getCurrentAperturaCaja()
    {
        return $this->_aperturaRepository->getCurrent();
    }

    public function getLastAperturaCaja()
    {
        return $this->_aperturaRepository->getLast();
    }

    public function openCaja($data)
    {
        try {
            DB::beginTransaction();

            $data['observaciones'] = $data['observaciones'] ?? 'NINGUNA';

            $apertura = AperturaCaja::create($data);

            $this->_configuracionRepository->set('ESTADO_CAJA', 'abierta');

            DB::commit();

            $this->_logger
                ->success()
                ->description('Se aperturó la caja correctamente.')
                ->after(json_encode($apertura))
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->log();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $this->_logger
                ->error()
                ->description('Error al aperturar la caja.')
                ->exception($e)
                ->user_id(Auth::user()->id)
                ->module($this::class)
                ->log();

            return false;
        }
    }

    public function closeCaja()
    {
        try {
            DB::beginTransaction();

            $apertura = $this->_aperturaRepository->getCurrent();
            $res = $this->_cierreCajaBuilder->calculateEverything();

            $apertura->update([
                ...$res,
                'fecha_hora_cierre' => date("Y-m-d H:i:s"),
                'estado' => AperturaCajaEstado::Concluido->value
            ]);

            $this->_configuracionRepository->set('ESTADO_CAJA', 'cerrada');
            DB::commit();

            $this->_logger
                ->success()
                ->description('Se cerró la caja correctamente.')
                ->after(json_encode($apertura->fresh()))
                ->link_id($apertura->id)
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->log();
        } catch (Exception $e) {

            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Error al cerrar caja.')
                ->before(json_encode($apertura))
                ->user_id(auth()->user()->id)
                ->exception($e)
                ->module($this::class)
                ->log();
        }
    }

    public function sell($data)
    {
        return $this->_ventasRepository->create($data);
    }
}
