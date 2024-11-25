<?php

namespace App\Http\Controllers;

use App\Helpers\Logger;
use App\PermisoTemp;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermisosTempController extends Controller
{
    public function __construct()
    {
        $this->middleware(["auth", "can:permisos-temporales.creation"]);
    }

    public function create($id)
    {
        $usuario = User::find($id);
        $permisosAsignados = $usuario->permissions->pluck("id")->toArray();
        $permisos = Permission::whereNotIn("id", $permisosAsignados)->orderBy("friendly_name")->get();
        return view("usuarios.permisos-temp", compact("usuario", "permisos"));
    }
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            "usuario_id" => "required",
            "permiso_id" => "required",
            "tiempo" => "required"
        ], [
            "usuario_id.required" => "Seleccione un usuario.",
            "permiso_id.required" => "Seleccione un permiso.",
            "tiempo.required" => "El tiempo de expiración es requerido."
        ]);
        if ($v->fails())
            return redirect()->route("permisos-temp.create", $request->usuario_id)->withInput()->withErrors($v->errors());

        $expiracion = date("Y-m-d H:i:s", strtotime("+" . $request->input("tiempo") . " minutes"));
        $request->merge([
            "expiracion" => $expiracion
        ]);
        try {
            DB::beginTransaction();
            // Log
            Logger::info(
                -1,
                "Se agregó permiso temporal",
                auth()->user()->id,
                null,
                json_encode($request->toArray()),
                "Permisos"
            );
            PermisoTemp::create($request->all());
            $permission = Permission::findById($request->input("permiso_id"));
            User::find($request->input("usuario_id"))->givePermissionTo($permission);
            $request->session()->flash("flash", [
                "kind" => "success",
                "msj" => "Permiso '{$permission->friendly_name}' concedido durante {$request->input("tiempo")} minutos"
            ]);
            DB::commit();
        } catch (\Exception $e) {
            // Log de errores
            Logger::error(
                -1,
                "{$request->route()->getAction('as')} {$request->getContent()}",
                auth()->user()->id,
                null,
                null,
                "{$request->route()->getAction('as')}",
                $e
            );
            DB::rollBack();
            return redirect()->route("permisos-temp.create", $request->usuario_id)->withInput()->withErrors(["general" => config("app.fatal")]);
        }
        return response()->json(["estado" => true]);
    }
}
