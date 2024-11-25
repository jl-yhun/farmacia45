<?php

namespace App\Repositories;

use App\Exceptions\StockLessThanZeroException;
use App\Helpers\LoggerBuilder;
use App\Perdida;
use Illuminate\Support\Facades\DB;

class PerdidasRepository implements IPerdidasRepository
{
    private $_logger;
    private $_productosRepository;

    public function __construct(LoggerBuilder $logger, IProductosRepository $productosRepository)
    {
        $this->_logger = $logger;
        $this->_productosRepository = $productosRepository;
    }

    public function get()
    {
        return Perdida::all();
    }

    public function show($id)
    {
        return Perdida::find($id);
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            $perdida = Perdida::create($data);
            $this->_productosRepository->modifyStockByAmount($perdida->producto);
            DB::commit();

            $this->_logger
                ->success('agregar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->after(json_encode($data))
                ->log();
            return true;
        } catch (StockLessThanZeroException $e) {
            DB::rollBack();

            $this->_logger
                ->error()
                ->description('Stock no puede ser menor a 0.')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->before(json_encode($data))
                ->exception($e)
                ->log();
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
            $dato = Perdida::find($id);
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
            Perdida::destroy($id);
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
