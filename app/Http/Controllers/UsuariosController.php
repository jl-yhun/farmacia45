<?php

namespace App\Http\Controllers;

use App\Http\Requests\Usuarios\PostRequest;
use App\Http\Requests\Usuarios\PutRequest;
use App\Repositories\IPermisosRepository;
use App\Repositories\IRolesRepository;
use App\Repositories\IUsuariosRepository;

class UsuariosController extends Controller
{
    private $_repository;
    private $_rolesRepository;

    public function __construct(
        IUsuariosRepository $repository,
        IRolesRepository $rolesRepository
    ) {
        $this->_repository = $repository;
        $this->_rolesRepository = $rolesRepository;

        $this->middleware(['auth']);
    }

    public function index()
    {
        $this->authorize('usuarios.view');

        $usuarios = $this->_repository->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $this->authorize('usuarios.creation');

        $roles = $this->_rolesRepository->getOrderedBy('name');

        return view('usuarios.create', compact('roles'));
    }

    public function store(PostRequest $request)
    {
        $this->authorize('usuarios.creation');

        if ($this->_repository->create($request->all()))
            return response()->json(['estado' => true]);
        else
            return redirect()->route('usuarios.create')
                ->withInput()
                ->withErrors(['general' => config('app.fatal')]);
    }

    public function edit($id)
    {
        $this->authorize('usuarios.update');

        $usuario = $this->_repository->show($id);
        $roles = $this->_rolesRepository->getOrderedBy('name');

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(PutRequest $request, $id)
    {
        $this->authorize('usuarios.update');

        if ($this->_repository->update($request->all(), $id))
            return response()->json(['estado' => true]);
        else
            return redirect()->route('usuarios.edit', ['usuario' => $id])
                ->withInput()
                ->withErrors(['general' => config('app.fatal')]);
    }

    public function destroy($id)
    {
        $this->authorize('usuarios.delete');

        if ($this->_repository->delete($id))
            return redirect()->route('usuarios.index')
                ->with('flash', [
                    'kind' => 'success',
                    'msj' => 'Eliminado correctamente !!'
                ]);
        else
            return redirect()->route('usuarios.index')
                ->withInput()
                ->withErrors(['general' => config('app.fatal')]);
    }
}
