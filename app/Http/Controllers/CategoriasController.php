<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categorias\PostRequest;
use App\Http\Requests\Categorias\PutRequest;
use App\Repositories\ICategoriasRepository;

class CategoriasController extends Controller
{
    private $_repository;

    public function __construct(ICategoriasRepository $repository)
    {
        $this->_repository = $repository;
        $this->middleware(['auth']);
    }

    public function index()
    {
        $this->authorize('categorias.view');

        $categorias = $this->_repository->get();
        return view('categorias.index', compact('categorias'));
    }

    // TODO: This is temporally, migrate categorias to Vue
    public function indexApi()
    {
        $this->authorize('categorias.view');

        $categorias = $this->_repository->get();

        return response()->json(['estado' => true, 'data' => $categorias]);
    }

    public function create()
    {
        $this->authorize('categorias.creation');

        return view('categorias.create');
    }

    public function store(PostRequest $request)
    {
        $this->authorize('categorias.creation');

        if ($this->_repository->create($request->all()))
            return response()->json(['estado' => true]);
        else
            return redirect()->route('categorias.create')
                ->withInput()
                ->withErrors(['general' => config('app.fatal')]);
    }

    public function show($id, $json = null)
    {
        $this->authorize('categorias.view');

        if ($json) {
            $data = $this->_repository->show($id);
            return response()->json(['categoria' => $data]);
        }

        $categoria = $this->_repository->show($id);
        return view('categorias.show', compact('categoria'));
    }

    public function edit($id)
    {
        $this->authorize('categorias.update');

        $categoria = $this->_repository->show($id);
        return view('categorias.edit', compact('categoria'));
    }

    public function update(PutRequest $request, $id)
    {
        $this->authorize('categorias.update');

        if ($this->_repository->update($request->all(), $id))
            return response()->json(['estado' => true]);
        else
            return redirect()->route('categorias.edit', ['categoria' => $id])
                ->withInput()
                ->withErrors(['general' => config('app.fatal')]);
    }

    public function destroy($id)
    {
        $this->authorize('categorias.delete');

        if ($this->_repository->delete($id))
            return redirect()->route('categorias.index')
                ->with('flash', [
                    'kind' => 'success',
                    'msj' => 'Eliminado correctamente !!'
                ]);
        else
            return redirect()->route('categorias.index')
                ->withInput()
                ->withErrors(['general' => config('app.fatal')]);
    }
}
