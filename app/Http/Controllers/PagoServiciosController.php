<?php

namespace App\Http\Controllers;

use App\Http\Requests\recargas\PostRequest as RecargasPostRequest;
use App\Http\Requests\servicios\PostRequest as ServiciosPostRequest;
use App\Repositories\IPagoServiciosRepository;
use Illuminate\Http\Request;

class PagoServiciosController extends Controller
{
    private $_repository;

    public function __construct(IPagoServiciosRepository $repository)
    {
        $this->_repository = $repository;
    }

    public function recargas(RecargasPostRequest $request)
    {
        return response()->json([
            'estado' => $this->_repository->createRecarga($request->all())
        ]);
    }

    public function servicios(ServiciosPostRequest $request)
    {
        return response()->json([
            'estado' => $this->_repository->createServicio($request->all())
        ]);
    }
}
