<?php

namespace App\Repositories;

use App\Helpers\LoggerBuilder;
use App\User;
use Illuminate\Support\Facades\DB;

class UsuariosRepository implements IUsuariosRepository
{
    private $_logger;
    private $_permisosRepository;

    public function __construct(LoggerBuilder $logger, IPermisosRepository $roleRepository)
    {
        $this->_logger = $logger;
        $this->_permisosRepository = $roleRepository;
    }

    public function get()
    {
        return User::all();
    }

    public function show($id)
    {
        return User::find($id);
    }

    private function prepareInfoForLog($data)
    {
        unset($data['password']);

        return $data;
    }

    public function create($data)
    {
        try {
            $dataForLog = $this->prepareInfoForLog($data);

            DB::beginTransaction();

            $usuario = User::create($data);
            $usuario->assignRole($data['role_id']);

            DB::commit();

            $this->_logger
                ->success('agregar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->after(json_encode($dataForLog))
                ->log();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error('agregar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->before(json_encode($dataForLog))
                ->exception($e)
                ->log();
        }

        return false;
    }

    public function update($data, $id)
    {
        try {
            $dataPrepared = $this->prepareInfoForLog($data);

            DB::beginTransaction();

            $usuario = User::find($id);
            $persistentData = $usuario->toArray();
            $usuario->update($dataPrepared);

            $usuario->assignRole($data['role_id']);
            $this->changePasswordIfApplicable($id, $data['password'] ?? '');

            DB::commit();

            $this->_logger
                ->success('actualizar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->before(json_encode($persistentData))
                ->after(json_encode($dataPrepared))
                ->log();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->_logger
                ->error('actualizar')
                ->user_id(auth()->user()->id)
                ->module($this::class)
                ->after(json_encode($dataPrepared))
                ->exception($e)
                ->log();
        }

        return false;
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            User::destroy($id);
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

    private function changePasswordIfApplicable($id, $password = '')
    {
        if ($password != '') {
            $user = $this->show($id);
            $user->password = $password;

            $this->_logger
                ->success()
                ->description('Se cambió contraseña de usuario ' . $user)
                ->user_id(auth()->user()->id)
                ->link_id($id)
                ->module($this::class)
                ->log();
        }
    }
}
