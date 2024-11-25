<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transferencias\PostRequest;
use App\Repositories\ITransferenciasRepository;
use Illuminate\Http\Request;

class TransferenciasController extends Controller
{
    private $_repository;

    public function __construct(ITransferenciasRepository $repository)
    {
        $this->_repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        return response()->json(['estado' => $this->_repository->create($request->all())]);
    }
}
