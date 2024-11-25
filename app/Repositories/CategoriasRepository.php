<?php

namespace App\Repositories;

use App\Categoria;
use App\Helpers\LoggerBuilder;
use Illuminate\Support\Facades\DB;

class CategoriasRepository implements ICategoriasRepository
{
    private $_logger;
    public function __construct(LoggerBuilder $logger)
    {
        $this->_logger = $logger;
    }

    public function get()
    {
        return Categoria::all();
    }

    public function show($id)
    {
        return Categoria::find($id);
    }

    public function create($data)
    {
        try {
            DB::beginTransaction();
            Categoria::create($data);
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
            $dato = Categoria::find($id);
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
            Categoria::destroy($id);
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
