<?php

namespace App\Repositories;

interface ICategoriasRepository
{
    public function get();
    public function show($id);
    public function create($data);
    public function update($data, $id);
    public function delete($id);
}
