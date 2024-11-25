<?php

namespace App\Http\Controllers;

use App\Repositories\ISimilaresRepository;
use Illuminate\Http\Request;

class SimilaresController extends Controller
{
    private $_repository;

    public function __construct(ISimilaresRepository $repository)
    {
        $this->_repository = $repository;
    }

    public function store(Request $request)
    {
        $this->authorize('similares.creation');

        return response()->json([
            'estado' => $this->_repository->create($request->all())
        ]);
    }

    public function index($id)
    {
        $this->authorize('similares.view');

        $result = $this->_repository->get($id);
        return response()->json(['estado' => true, 'data' => $result]);
    }
}
