<?php

namespace App\Http\Controllers;

use App\Helpers\LoggerBuilder;
use App\Http\Requests\OrdenesCompra\Items\PatchRequest;
use App\Http\Requests\OrdenesCompra\Items\PostRequest;
use App\Repositories\IOrdenesCompraRepository;
use Exception;
use Illuminate\Http\Request;

class OrdenesCompraController extends Controller
{
    private $_repository;
    private $_logger;

    public function __construct(IOrdenesCompraRepository $repository, LoggerBuilder $logger)
    {
        $this->_repository = $repository;
        $this->_logger = $logger;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('ordenes-compra.view');
        try {

            $result = $this->_repository->getOrdered();
            return response()->json(['estado' => true, 'data' => $result]);
        } catch (Exception $e) {
            $this->_logger
                ->error()
                ->description('Error al cargar las órdenes de compra.')
                ->exception($e)
                ->user_id(auth()->user()->id)
                ->log();
        }
        return response()->json(['estado' => false]);
    }

    public function suggested($id = 0)
    {
        $this->authorize('ordenes-compra.view');
        try {

            $result = $this->_repository->suggested($id);
            return response()->json(['estado' => true, 'data' => $result]);
        } catch (Exception $e) {
            dd($e);
            $this->_logger
                ->error()
                ->description('Error al cargar la lista de compras sugeridas.')
                ->exception($e)
                ->user_id(auth()->user()->id)
                ->log();
        }
        return response()->json(['estado' => false]);
    }

    public function notAvailable($id = 0)
    {
        $this->authorize('ordenes-compra.view');
        try {

            $result = $this->_repository->notAvailable($id);
            return response()->json(['estado' => true, 'data' => $result]);
        } catch (Exception $e) {
            $this->_logger
                ->error()
                ->description('Error al cargar la lista de productos no disponibles.')
                ->exception($e)
                ->user_id(auth()->user()->id)
                ->log();
        }
        return response()->json(['estado' => false]);
    }

    public function addItem(PostRequest $request)
    {
        $this->authorize('ordenes-compra.creation');
        try {
            $this->_repository->addItem($request->all());

            return response()->json(['estado' => true]);
        } catch (Exception $e) {
            return response()->json(['estado' => false]);
        }
    }

    public function patchItem($ocId, $productoId, PatchRequest $request)
    {
        $this->authorize('ordenes-compra.update');
        try {
            $this->_repository->patchItem($ocId, $productoId, $request->all());

            return response()->json(['estado' => true]);
        } catch (Exception $e) {
            return response()->json(['estado' => false]);
        }
    }

    public function deleteItem($ocId, $productoId)
    {
        $this->authorize('ordenes-compra.update');
        try {
            $this->_repository->deleteItem($ocId, $productoId);

            return response()->json(['estado' => true]);
        } catch (Exception $e) {
            return response()->json(['estado' => false]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('ordenes-compra.view');
        try {
            $orden = $this->_repository->show($id);

            if (!$orden)
                return response('', 404);

            return response()->json(['estado' => true, 'data' => $orden]);
        } catch (Exception $e) {
            return response()->json(['estado' => false]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('ordenes-compra.update');

        try {
            $this->_repository->patch($id, $request->all());

            return response()->json(['estado' => true]);
        } catch (\Throwable $th) {
            return response()->json(['estado' => false]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
