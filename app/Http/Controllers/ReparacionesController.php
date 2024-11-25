<?php

namespace App\Http\Controllers;

use App\Reparacion;
use App\ReparacionAbono;
use App\Venta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ReparacionesController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth")->except("store");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reparaciones = Reparacion::where("estado", "<>", "Entregado")->get();

        return view("reparaciones.index", compact("reparaciones"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("reparaciones.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            "cliente" => "required",
            "marca" => "required",
            "falla" => "required",
            "fecha_entrega" => "required"
        ], [
            "cliente.required" => "El nombre del cliente es necesario.",
            "marca.required" => "La marca del equipo es necesaria.",
            "falla.required" => "La falla del equipo es necesaria.",
            "fecha_entrega.required" => "La fecha de entrega es necesaria."
        ]);
        if ($v->fails())
            return response()->json(["estado" => false, "errs" => $v->errors()->all()]);

        $rep = new Reparacion();
        try {
            $rep->usuario_id = auth()->user()->id;
            $rep->folio = "";
            $rep->marca = $request->marca;
            $rep->modelo = $request->modelo ?? "NA";
            $rep->cliente = $request->cliente;
            $rep->telefono = $request->telefono ?? "NA";
            $rep->falla = $request->falla;
            $rep->costo = $request->costo == "" || $request->costo == "0" ? -1 : $request->costo;
            $rep->abono = $request->abono == "" || $request->abono == "0" ? -1 : $request->abono;
            $rep->fecha_entrega = $request->fecha_entrega;
            $rep->observaciones = $request->observaciones ?? "NA";
            $rep->save();
            $rep->folio = date("dmy") . "-" . $rep->id;
            $rep->save();
        } catch (Exception $e) {
            Log::error('Reparaciones:store ---------- ' . $e->__toString());
            return response()->json(["estado" => false, "errs" => ["Ocurrió un error al registrar la reparación. Consultelo con soporte."]]);
        }
        return response()->json(["estado" => true, "data" => $rep]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $re = Reparacion::find($id);
        return response()->json(["data" => $re]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // return response()->json(App::getLocale());
        $reparacion = Reparacion::find($id);
        return view("reparaciones.edit", compact("reparacion"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $v = Validator::make($request->all(), [
            "cliente" => "required",
            "marca" => "required",
            "falla" => "required",
            "fecha_entrega" => "required",
            "costo" => "min:0|not_in:0"
        ], [
            "cliente.required" => "El nombre del cliente es necesario.",
            "marca.required" => "La marca del equipo es necesaria.",
            "falla.required" => "La falla del equipo es necesaria.",
            "fecha_entrega.required" => "La fecha de entrega es necesaria.",
            "costo.not_in" => "El costo no puede ser 0",
            "costo.min" => "El costo no puede ser menor a 0."
        ]);
        $v->validate();

        $rep = Reparacion::find($id);
        try {
            $rep->marca = $request->marca;
            $rep->modelo = $request->modelo ?? "NA";
            $rep->cliente = $request->cliente;
            $rep->telefono = $request->telefono ?? "NA";
            $rep->falla = $request->falla;
            $rep->costo = $request->costo ?? -1;
            $rep->observaciones = $request->observaciones ?? "NA";
            $rep->save();
        } catch (Exception $e) {
            Log::error('Reparaciones:update ---------- ' . $e->__toString());
            return redirect()->back()->withErrors(["Ocurrió un error al actualizar la reparación. Consultelo con soporte."])->withInput();
        }
        return redirect()->route("reparaciones.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function abonar($id)
    {
        $reparacion = Reparacion::find($id);

        return view("reparaciones.abonar", compact("reparacion"));
    }
    public function realizarAbono(Request $request)
    {
        try {
            DB::beginTransaction();
            $v = Validator::make($request->all(), [
                "monto" => "numeric|min:1"
            ], [
                "monto.numeric" => "Monto no válido"
            ]);
            if ($v->fails())
                return response()->json(["estado" => false, "_m" => $v->errors()->all()]);
            // Se busca la reparación
            $reparacion = Reparacion::find($request->id);
            // SI el monto excede el costo - abono
            if ($request->monto > ($reparacion->costo - $reparacion->abono))
                return response()->json(["estado" => false, "_m" => ["El monto excede el costo total, tomando en cuenta el abono ya dejado."]]);

            $abono = new ReparacionAbono();
            $abono->reparacion_id = $reparacion->id;
            $abono->monto = $request->monto;
            $abono->save();
            $reparacion->abono = $reparacion->abono == -1 ? $request->monto : ($reparacion->abono + $request->monto);
            $reparacion->save();
            DB::commit();
            return response()->json(["estado" => true, "data" => $reparacion]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Reparaciones:realizarAbono ---------- ' . $e->__toString());
            return response()->json(["estado" => false, "_m" => ["Error al registrar el abono, alternativamente anótelo en el ticket del cliente"]]);
        }
    }
    public function entregar($id)
    {
        try {
            DB::beginTransaction();
            // Se busca la reparación
            $reparacion = Reparacion::find($id);
            $reparacion->estado = "Entregado";
            $reparacion->fecha_entrega = date("Y-m-d");
            $reparacion->save();
            DB::commit();
            return response()->json(["estado" => true, "data" => $reparacion]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Reparaciones:entregar ---------- ' . $e->__toString());
            return response()->json(["estado" => false, "_m" => ["Error al entregar el equipo, entregue el equipo e inténtelo de nuevo más tarde."]]);
        }
    }
}
