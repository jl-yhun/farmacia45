<?php

namespace App\Http\Controllers;

use App\Exceptions\ApartadosAmountUnavailable;
use App\Http\Requests\Apartados\PostRequest;
use App\Repositories\IApartadosRepository;
use Illuminate\Http\Request;

class ApartadosController extends Controller
{
    private $_repository;

    public function __construct(IApartadosRepository $repository)
    {
        $this->_repository = $repository;
    }

    public function index()
    {

        $this->authorize('apartados.view');

        try {
            $result = $this->_repository->get();

            return response()->json(['estado' => true, 'data' => $result]);
        } catch (\Throwable) {
            return response()->json(['estado' => false]);
        }
    }

    public function store(PostRequest $request)
    {
        $this->authorize('apartados.creation');

        try {

            $this->_repository->create($request->all());
            return response()->json(['estado' => true]);
        } catch (ApartadosAmountUnavailable) {
            return response()->json(['message' => 'Monto no disponible en apartados.'], 500);
        } catch (\Throwable) {
            return response()->json(['estado' => false]);
        }
    }
}
