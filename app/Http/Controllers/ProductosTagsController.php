<?php

namespace App\Http\Controllers;

use App\Http\Requests\Productos\PatchRequest;
use App\Http\Requests\Productos\PostRequest;
use App\Http\Requests\ProductosProveedores\PostRequest as ProductosProveedoresPostRequest;
use App\Http\Requests\ProductosProveedores\PutRequest;
use App\Http\Requests\Tags\LinkRequest;
use App\Producto;
use App\Repositories\ICategoriasRepository;
use App\Repositories\IProductosRepository;
use App\Repositories\ITagRepository;
use App\Tag;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class ProductosTagsController extends Controller
{
    private $_repository;

    public function __construct(
        ITagRepository $repository,
    ) {
        $this->_repository = $repository;
        $this->middleware(['auth']);
    }

    public function index(Producto $producto)
    {
        try {
            return response()->json([
                'estado' => true,
                'data' => $this->_repository->getByTageable(Producto::class, $producto)
            ]);
        } catch (Throwable) {
            return response()->json([
                'estado' => false
            ]);
        }
    }

    public function store(LinkRequest $request, $id)
    {
        // TODO: Remove try catch at controller level
        try {
            return response()->json([
                'estado' => $this->_repository->link(Producto::class, $id, $request->all())
            ]);
        } catch (\Throwable) {
            return response()->json([
                'estado' => false
            ]);
        }
    }

    public function destroy(Producto $producto, Tag $tag)
    {
        return response()->json([
            'estado' => $this->_repository->unlink(Producto::class, $producto, $tag)
        ]);
    }
}
