<?php

namespace App\Http\Controllers;

use App\Garantia;
use App\Perdida;
use App\Producto;
use App\Venta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class GarantiasController extends Controller
{
    protected $singular = "garantia";
    protected $plural = "garantias";
    protected $modelo = Garantia::class;
    protected $validatorStore = [
        "venta_id" => "required",
        "producto_id" => "exclude_if:tipo,CANCELACIÓN",
        "tipo" => "required",
        "productos" => "required_if:tipo,CAMBIO|required_if:tipo,GARANTÍA",
        "perdida" => "required"
    ];
    protected $validationMessages = [
        "venta_id.required" => "El folio de la venta es requerido.",
        "producto_id.required" => "El producto devuelto es requerido.",
        "tipo.required" => "Seleccione un tipo de proceso.",
        "productos.required_if" => "Debe agregar al menos un Nuevo Producto.",
        "perdida.required" => "Indique si el producto devuelto es una pérdida."
    ];
    protected $data = [];

    public function __construct()
    {
        // parent::__construct();

        $this->middleware(["auth"]);
    }
    public function resolucion(Garantia $garantia)
    {

        return view("garantias.resolucion", compact("garantia"));
    }

    public function venta(Request $request, $folio)
    {
        $venta = Venta::find($folio);
        if (!$venta)
            return response("", 404);
        return view("garantias.venta", compact("venta"));
    }

    public function productos($criteria, $productoId)
    {
        $productos = Producto::where(function ($q) use ($criteria) {
            $q->where("nombre", "like", "%$criteria%")->orWhere("id", $criteria);
        })->orderBy("nombre")->get();

        if ($productos->count() == 0)
            return response("", 404);

        return view("productos.busqueda", compact("productos"));
    }

    public function storeCallback(Model $model, Request $request)
    {
        $productoDevuelto = Producto::find($request->input("producto_id") ?? 0);
        if ($request->input("tipo") == "DEVOLUCIÓN DE DINERO") {
            if (!auth()->user()->hasPermissionTo("garantias.devoluciones.creation")) {
                DB::rollBack();
                return redirect()->route("garantias.create")->withInput()->withErrors(["general" => config("app.no_granted")]);
            }
        }
        $productos = $request->input("productos");
        $productosC = collect($productos);

        $model->productos_nuevos()->sync($productosC->pluck("id"));


        if ($request->input("tipo") == "CANCELACIÓN") {
            // Esto pasará cuando se haya hecho una cancelación de la venta
            // Se encuentran todos los productos involucrados para regresar al stock
            foreach ($model->venta->productos as $producto) {
                $producto->stock += $producto->pivot->cantidad;
                $producto->save();
            }
        } else {
            if ($request->input("perdida")) {
                // El producto devuelto debe ir a pérdidas
                $perdida = new Perdida();
                $perdida->usuario_id = auth()->user()->id;
                $perdida->producto_id = $productoDevuelto->id;
                $perdida->garantia_id = $model->id;
                $perdida->motivo = "PÉRDIDA POR " . $request->input("tipo") . " VER LA RESOLUCIÓN PARA MÁS INFORMACIÓN.";
                $perdida->save();
            } else {
                // DEVOLUCIÓN DE DINERO y NO PÉRDIDA
                // CAMBIO Y NO PÉRDIDA
                $productoDevuelto->stock++;
                $productoDevuelto->save();
            }
        }
        foreach ($productosC as $producto) {
            $prod = Producto::find($producto["id"]);
            $prod->stock -= $producto["cantidad"];
            $prod->save();
            $model->productos_nuevos()->updateExistingPivot($producto["id"], [
                "cantidad" => $producto["cantidad"]
            ]);
        }
        return true;
    }

    public function beforeStoreCallback(Request $request)
    {
        $venta = Venta::find($request->input("venta_id"));
        // Si es cancelación sólo se permite una venta del mismo día
        if ($request->input("tipo") == "CANCELACIÓN") {
            if (!($venta->created_at >= date("Y-m-d 00:00:00") && $venta->created_at <= date("Y-m-d 23:59:59"))) {
                return redirect()->route("garantias.create")
                    ->withInput()
                    ->withErrors(["venta" => "Esta venta no es de hoy, aplique DEVOLUCIÓN DE DINERO en este caso."]);
            }
            if (count($venta->productosReclamados) > 0)
                return redirect()->route("garantias.create")
                    ->withInput()
                    ->withErrors(["venta" => "Se ha aplicado GARANTÍA/CAMBIO a algunos productos de esta venta. Imposible cancelar."]);
        }
        // Se verifica que la venta no tenga garantía aplicada con aterioridad
        if ($venta->garantiaAplicada) {
            return redirect()->route("garantias.create")
                ->withInput()
                ->withErrors(["venta" => "A esta venta ya se le aplicó GARANTÍA/CAMBIO anteriormente."]);
        }
        return true;
    }
}
