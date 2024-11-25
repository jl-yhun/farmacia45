<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\Inventario\PostRequest;
use App\Repositories\IInventarioRepository;
use App\Repositories\IProductosRepository;
use Exception;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    private $_repository;

    public function __construct(
        IInventarioRepository $repository
    ) {
        $this->_repository = $repository;
    }

    public function index()
    {
        $result = $this->_repository->get();
        if ($result !== false)
            return response()->json(['estado' => true, 'data' => $result]);
        else
            return response()->json(['estado' => false]);
    }

    public function store(PostRequest $request)
    {
        try {
            $this->_repository->create($request->all());

            $item = $this->_repository->show($request->codigo_barras);


            return response()->json([
                'estado' => true,
                'data' => $item
            ]);
        } catch (ProductNotFoundException) {
            return response()->json([
                'estado' => false,
                'data' => 'No existe el producto.'
            ]);
        } catch (Exception) {
            return response()->json([
                'estado' => true,
                'data' => $this->_repository->show($request->codigo_barras)
            ]);
        }
    }

    public function update(Request $request, $codigo_barras)
    {
        return response()->json([
            'estado' => $this->_repository->update($request->all(), $codigo_barras)
        ]);
    }

    public function destroy($id)
    {
        return response()->json(['estado' => $this->_repository->destroy($id)]);
    }

    public function diff()
    {
        $result = $this->_repository->diff();
        
        if ($result !== false)
            return response()->json(['estado' => true, 'data' => $result]);
        else
            return response()->json(['estado' => false]);
    }

    public function finish()
    {
        return response()->json(['estado' => $this->_repository->finish()]);
    }
}
