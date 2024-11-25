<?php

namespace App\Http\Controllers;

use App\Http\Requests\Perdidas\PostRequest;
use App\Repositories\IPerdidasRepository;
use App\Repositories\IProductosRepository;

class PerdidasController extends Controller
{
    private $_repository;
    private $_productosRepository;

    public function __construct(
        IPerdidasRepository $repository,
        IProductosRepository $productosRepository
    ) {
        $this->_repository = $repository;
        $this->_productosRepository = $productosRepository;
        $this->middleware(['auth']);
    }

    public function index()
    {
        $this->authorize('perdidas.view');

        $perdidas = $this->_repository->get();
        return view('perdidas.index', compact('perdidas'));
    }

    public function create()
    {
        $this->authorize('perdidas.creation');

        $productos = $this->_productosRepository->getOrderedBy('nombre');

        return view('perdidas.create', compact('productos'));
    }

    public function store(PostRequest $request)
    {
        $this->authorize('perdidas.creation');

        if ($this->_repository->create($request->all()))
            return response()->json(['estado' => true]);
        else {
            $productos = $this->_productosRepository->getOrderedBy('nombre');
            return redirect()->route('perdidas.create', compact('productos'))
                ->withInput()
                ->withErrors(['general' => config('app.fatal')]);
        }
    }

    public function productos($criteria)
    {
        $productos = $this->_productosRepository->search($criteria);

        return view('productos.busqueda', compact('productos'));
    }
}
