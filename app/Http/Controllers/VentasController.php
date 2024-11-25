<?php

namespace App\Http\Controllers;

use App\Http\Requests\CobrarPostRequest;
use App\Repositories\IVentasRepository;
use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VentasController extends Controller
{
    protected $singular = "venta";
    protected $plural = "ventas";
    protected $modelo = Venta::class;
    private $_ventaRepository;

    public function __construct(IVentasRepository $ventasRepository)
    {
        // parent::__construct();
        $this->middleware(["auth"]);
        $this->_ventaRepository = $ventasRepository;
    }

    public function showJson($id)
    {
        $venta = $this->_ventaRepository->show($id);
        if ($venta)
            return response()->json($venta);
        return response()->json(status: Response::HTTP_NOT_FOUND);
    }

    public function show($id)
    {
        $venta = $this->_ventaRepository->show($id);

        return view('ventas.show', compact('venta'));
    }

    public function ultima()
    {
        $venta = Venta::orderBy('id', 'desc')->first();
        return response($venta->id);
    }

    public function create(CobrarPostRequest $request)
    {
        if ($this->_ventaRepository->create($request->all())) {
            $venta = $this->_ventaRepository->getLast();
            // TODO: Pass correct venta id or search for another solution
            return response()->json(['callback' => 'imprimirTicketVenta', 'params' => ['id' => $venta->id]]);
        } else
            return response()->json(['estado' => false]);
    }

    public function destroy(Venta $venta)
    {
        $this->authorize('ventas.delete');

        return response()->json(['estado' => $this->_ventaRepository->delete($venta)]);
    }

    public function reportar(Request $request)
    {

        try {
            $ventas = $this->_ventaRepository->reportByDateRange($request->query('startDate'), $request->query('endDate'));

            return response()->json([
                'estado' => true,
                'data' => $ventas
            ]);
        } catch (\Throwable $e) {
            dd($e);
            return response()->json([
                'estado' => false
            ]);
        }
    }
}
