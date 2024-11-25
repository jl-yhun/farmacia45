<?php

namespace App\Http\Controllers;

use App\Repositories\IProveedoresRepository;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class ProveedoresController extends Controller
{
    private $_repository;

    public function __construct(IProveedoresRepository $repository)
    {
        $this->_repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('proveedores.view');

        try {
            $data = $this->_repository->get();

            return response()->json([
                'estado' => true,
                'data' => $data
            ]);
        } catch (\Throwable) {

            return response()->json([
                'estado' => false
            ]);
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
        //
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
        //
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
