<?php

namespace App\Repositories;

use App\Classes\ICierreAperturaCajaBuilder;
use App\Gasto;
use App\Helpers\LoggerBuilder;
use Illuminate\Support\Facades\DB;

class GastosRepository implements IGastosRepository
{
    private $_logger;
    private $_apertura;

    public function __construct(
        LoggerBuilder $logger,
        IAperturasCajaRepository $apertura
    ) {
        $this->_logger = $logger;
        $this->_apertura = $apertura;
    }

    public function get()
    {
        if (auth()->user()->hasRole('Admin'))
            // Si el usuario es Admin obtenemos todos los registros
            return Gasto::all();
        else
            // Si no es Admin entonces obtenemos los registros de este corte de caja
            return Gasto::where('apertura_caja_id', getAperturaCajaIfExist())->get();
    }

    public function show($id)
    {
        return Gasto::find($id);
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            $apertura = $this->_apertura->getCurrent();
            Gasto::create([
                ...$data,
                'usuario_id' => auth()->user()->id,
                'apertura_caja_id' => $apertura->id
            ]);
            DB::commit();

            $this->_logger
                ->success('agregar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->after(json_encode($data))
                ->log();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();

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

    public function update($data, $id)
    {
        try {
            DB::beginTransaction();
            $dato = Gasto::find($id);
            $persistentData = $dato->toArray();
            $dato->update($data);
            DB::commit();

            $this->_logger
                ->success('actualizar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->before(json_encode($persistentData))
                ->after(json_encode($data))
                ->log();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error('actualizar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->after(json_encode($data))
                ->exception($e)
                ->log();
        }

        return false;
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            Gasto::destroy($id);
            DB::commit();

            $this->_logger
                ->success('eliminar')
                ->user_id(auth()->user()->id)
                ->link_id($id)
                ->module($this::class)
                ->log();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error('eliminar')
                ->user_id(auth()->user()->id)
                ->link_id($id)
                ->module($this::class)
                ->exception($e)
                ->log();
        }

        return false;
    }
}
