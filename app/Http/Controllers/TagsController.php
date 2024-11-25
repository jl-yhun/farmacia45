<?php

namespace App\Http\Controllers;

use App\Repositories\ITagRepository;
use Throwable;

class TagsController extends Controller
{
    private $_repository;

    public function __construct(
        ITagRepository $repository,
    ) {
        $this->_repository = $repository;
        $this->middleware(['auth']);
    }

    public function index()
    {
        try {
            return response()->json([
                'estado' => true,
                'data' => $this->_repository->all()
            ]);
        } catch (Throwable $e) {
            dd($e);
            return response()->json([
                'estado' => false
            ]);
        }
    }

}
