<?php

namespace App\Http\Controllers;

use App\Http\Requests\PuntoVentaOpenRequest;
use App\Producto;
use App\Repositories\IProductosRepository;
use App\Repositories\IPuntoVentaRepository;
use Illuminate\Http\Request;

class PuntoVentaController extends Controller
{
    private $_repository;
    private $_productosRepository;

    public function __construct(
        IPuntoVentaRepository $repository,
        IProductosRepository $productosRepository
    ) {
        $this->_repository = $repository;
        $this->_productosRepository = $productosRepository;

        $this->middleware("auth")->except('index');
    }

    public function index()
    {
        return view('punto-venta.index');
    }

    public function descuento($id)
    {
        $producto = $this->_productosRepository->show($id);

        if (!$producto)
            return response(status: 404);

        $this->_productosRepository->makePurchasePriceVisibleTemporally($producto);

        return view('punto-venta.modal-descuento', compact('producto'));
    }

    public function cobro()
    {
        return view('punto-venta.modal-pago');
    }

    public function reprintLast()
    {
        $apertura = $this->_repository->getLastAperturaCaja();

        return response()->json($apertura);
    }

    public function opening()
    {
        return view('punto-venta.cambiar-estado-caja');
    }

    public function open(PuntoVentaOpenRequest $request)
    {
        if ($this->_repository->existsAperturaCaja())
            return response()->json(["estado" => true]);

        if ($this->_repository->openCaja($request->all()))
            return response()->json(["estado" => true]);
        else // TODO: Normalize responses, sometimes flash, json, redirects
            $request->session()->flash("flash", [
                "kind" => "danger",
                "msj" => config("app.fatal")
            ]);
    }

    public function close()
    {
        $this->_repository->closeCaja();

        return redirect()->route('punto-venta');
    }
}
