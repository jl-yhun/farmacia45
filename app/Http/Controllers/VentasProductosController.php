<?php

namespace App\Http\Controllers;

use App\Producto;
use App\Repositories\IVentasRepository;
use App\Venta;
use Illuminate\Http\Request;

class VentasProductosController extends Controller
{

    private $_ventasRepository;

    public function __construct(IVentasRepository $ventasRepository)
    {
        $this->_ventasRepository = $ventasRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Venta $venta, Producto $producto)
    {

        return response()->json([
            'estado' => $this->_ventasRepository->deleteItem($venta, $producto)
        ]);
    }
}
