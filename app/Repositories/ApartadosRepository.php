<?php

namespace App\Repositories;

use App\Apartado;
use App\Classes\ICierreAperturaCajaBuilder;
use App\Exceptions\ApartadosAmountUnavailable;
use App\Gasto;
use App\Helpers\LoggerBuilder;
use Exception;
use Illuminate\Support\Facades\DB;

class ApartadosRepository implements IApartadosRepository
{
    private $_logger;
    private $_apertura;

    public function __construct(
        LoggerBuilder $logger,
        IAperturasCajaRepository $apertura
    ) {
        $this->_logger = $logger;
        $this->_apertura = $apertura;

        $this->_logger
            ->module($this::class);

        if (auth()->user())
            $this->_logger
                ->user_id(auth()->user()->id);
    }

    public function get()
    {
        try {
            if (auth()->user()->hasRole('Admin'))
                // if admin then show everything
                return Apartado::orderBy('id', 'desc')->take(50)->get();
            else
                return Apartado::where('usuario_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->take(50)
                    ->get();
        } catch (\Throwable $th) {
            $this->_logger
                ->error()
                ->description('Error al obtener lista apartados.')
                ->exception($th)
                ->log();

            throw $th;
        }
    }

    // public function show($id)
    // {
    //     return Gasto::find($id);
    // }

    public function create($data)
    {
        try {
            DB::beginTransaction();

            $apertura = $this->_apertura->getCurrent();

            if ($this->getAmountAvailable($data['monto']) < 0)
                throw new ApartadosAmountUnavailable();

            Apartado::create([
                ...$data,
                'usuario_id' => auth()->user()->id,
                'apertura_caja_id' => $apertura->id
            ]);

            DB::commit();

            $this->_logger
                ->success('agregar')
                ->after(json_encode($data))
                ->log();
        } catch (ApartadosAmountUnavailable $e) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Se intentó exceder apartados.')
                ->before(json_encode($data))
                ->exception($e)
                ->log();

            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->_logger
                ->error('agregar')
                ->before(json_encode($data))
                ->exception($e)
                ->log();

            throw $e;
        }
    }

    private function getAmountAvailable($desiredAmount)
    {
        $amount = Apartado::sum('monto') + $desiredAmount;

        return $amount;
    }

    // public function update($data, $id)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $dato = Gasto::find($id);
    //         $persistentData = $dato->toArray();
    //         $dato->update($data);
    //         DB::commit();

    //         $this->_logger
    //             ->success('actualizar')
    //             ->user_id(auth()->user()->id)
    //             ->module($this::class)
    //             ->before(json_encode($persistentData))
    //             ->after(json_encode($data))
    //             ->log();
    //         return true;
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         $this->_logger
    //             ->error('actualizar')
    //             ->user_id(auth()->user()->id)
    //             ->module($this::class)
    //             ->after(json_encode($data))
    //             ->exception($e)
    //             ->log();
    //     }

    //     return false;
    // }

    // public function delete($id)
    // {
    //     try {
    //         DB::beginTransaction();
    //         Gasto::destroy($id);
    //         DB::commit();

    //         $this->_logger
    //             ->success('eliminar')
    //             ->user_id(auth()->user()->id)
    //             ->link_id($id)
    //             ->module($this::class)
    //             ->log();
    //         return true;
    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         $this->_logger
    //             ->error('eliminar')
    //             ->user_id(auth()->user()->id)
    //             ->link_id($id)
    //             ->module($this::class)
    //             ->exception($e)
    //             ->log();
    //     }

    //     return false;
    // }
}
