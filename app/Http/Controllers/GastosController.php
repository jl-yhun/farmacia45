<?php

namespace App\Http\Controllers;

use App\Http\Requests\Gastos\PostRequest;
use App\Repositories\IGastosRepository;

class GastosController extends Controller
{
    private $_repository;

    public function __construct(IGastosRepository $repository)
    {
        $this->_repository = $repository;
        $this->middleware(['auth']);
    }

    public function index()
    {
        $this->authorize('gastos.view');

        $gastos = $this->_repository->get();
        return view('gastos.index', compact('gastos'));
    }

    public function create()
    {
        $this->authorize('gastos.creation');

        return view('gastos.create');
    }

    public function store(PostRequest $request)
    {
        $this->authorize('gastos.creation');

        return response()->json(['estado' => $this->_repository->create($request->all())]);
    }

    public function destroy($id)
    {
        $this->authorize('gastos.delete');

        if ($this->_repository->delete($id))
            return redirect()->route('gastos.index')
                ->with('flash', [
                    'kind' => 'success',
                    'msj' => 'Eliminado correctamente !!'
                ]);
        else
            return redirect()->route('gastos.index')
                ->withInput()
                ->withErrors(['general' => config('app.fatal')]);
    }
}
