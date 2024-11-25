<?php

namespace App\Http\Controllers;

use App\Classes\ProductosQuery;
use App\Http\Requests\Productos\PatchRequest;
use App\Http\Requests\Productos\PostRequest;
use App\Http\Requests\ProductosProveedores\PostRequest as ProductosProveedoresPostRequest;
use App\Http\Requests\ProductosProveedores\PutRequest;
use App\Repositories\ICategoriasRepository;
use App\Repositories\IProductosRepository;
use Exception;
use Illuminate\Http\Request;

class ProductosController extends Controller
{
    private $_repository;
    private $_categoriesRepository;

    public function __construct(
        IProductosRepository $repository,
        ICategoriasRepository $categoriasRepository
    ) {
        $this->_repository = $repository;
        $this->_categoriesRepository = $categoriasRepository;
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $this->authorize('productos.view');
        try {
            $productos = $this->_repository->get($request->query);

            return response()->json(['estado' => true, 'data' => $productos]);
        } catch (Exception) {
            return response()->json(['estado' => false]);
        }
    }

    public function indexJson()
    {
        $this->authorize('productos.view');

        $productos = $this->_repository->all();

        return response()->json($productos);
    }

    public function proveedores($id)
    {
        try {
            $producto = $this->_repository->show($id);

            return response()->json([
                'estado' => true,
                'data' => $producto->proveedores
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'estado' => false
            ]);
        }
    }

    public function proveedoresStore($id, ProductosProveedoresPostRequest $input)
    {
        $this->authorize('productos-proveedores.creation');

        try {
            $result = $this->_repository->linkProveedor($id, $input->all());

            return response()->json([
                'estado' => true,
                'data' => $result
            ]);
        } catch (\Throwable) {
            return response()->json([
                'estado' => false
            ]);
        }
    }

    public function proveedoresUpdate($id, $proveedor_id, PutRequest $input)
    {
        $this->authorize('productos-proveedores.update');

        try {
            $result = $this->_repository->updateLinkedProveedor($id, $proveedor_id, $input->all());

            return response()->json([
                'estado' => true,
                'data' => $result
            ]);
        } catch (\Throwable) {
            return response()->json([
                'estado' => false
            ]);
        }
    }

    public function proveedoresDelete($id, $proveedor_id)
    {
        $this->authorize('productos-proveedores.delete');

        try {
            $this->_repository->deleteLinkedProveedor($id, $proveedor_id);

            return response()->json([
                'estado' => true
            ]);
        } catch (\Throwable) {
            return response()->json([
                'estado' => false
            ]);
        }
    }

    public function datatable()
    {
        $productos = $this->_repository->get();

        return datatable($productos);
    }

    public function buscar(Request $request)
    {
        $busqueda = $request->input('busqueda');

        $productos = $this->_repository->search($busqueda);

        return view('layouts.resultado-busqueda-modal', compact('busqueda', 'productos'));
    }

    public function create()
    {
        $this->authorize('productos.creation');
        $categorias = $this->_categoriesRepository->get();

        return view('productos.create', compact('categorias'));
    }

    public function store(PostRequest $request)
    {
        $this->authorize('productos.creation');

        try {

            $data = $this->_repository->create($request->all());
            return response()->json(['estado' => true, 'data' => $data]);
        } catch (Exception) {
            return response()->json(['estado' => false]);
        }
    }

    public function update(PatchRequest $request, $id = null)
    {
        $this->authorize('productos.update');

        $producto = $this->_repository->show($id);

        return response()->json([
            'estado' => $this->_repository->update($request->all(), $producto)
        ]);
    }

    public function destroy($id)
    {
        $this->authorize('productos.delete');

        return response()->json(['estado' => $this->_repository->delete($id)]);
    }
}
