<?php

namespace App\Repositories;

interface ISimilaresRepository
{
    public function get($id);
    public function create($productosIds);
}
