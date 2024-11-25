<?php

namespace App\Http\Controllers;

use App\Imports\SurtimientoImport;
use App\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SurtimientoController extends Controller
{
    public function index(){

    }
    public function procesar(Request $request){
        // $this->validate($request,[
        //     'archivo'  => 'required|mimes:xls,xlsx'
        //     ]);
       
        $surtimiento = new SurtimientoImport;
        Excel::import($surtimiento, $request->file("archivo"));
        $datos = $surtimiento->data;
        return view("surtimiento.index", compact("datos"));
    }
    public function guardar(Request $request) {
        DB::beginTransaction();
        try{
            foreach($request->productos as $producto){
                $p = Producto::find($producto["folio"]);
                if($p){
                    // Ya existe el producto
                    $p->compra = $producto["compra"];
                    $p->venta = $producto["venta"];
                    $p->stock = $producto["stock"];
                    $p->save();
                }else{
                    $prod = new Producto();
                    $prod->nombre = $producto["nombre"];
                    $prod->compra = $producto["compra"];
                    $prod->venta = $producto["venta"];
                    $prod->stock = $producto["cantidad"];
                    $prod->save();
                }
            }
            DB::commit();
            $request->session()->flash("estado","Guardado correctamente");
            return response()->json(["estado" =>true]);
        }catch(Exception $e){
            DB::rollBack();
            $request->session()->flash("estado","Error al adjuntar, consulte con el Administrador");
            Log::info("Error en surtimiento " . $e);
            return response()->json(["estado" =>false]);
        }
    }
}
