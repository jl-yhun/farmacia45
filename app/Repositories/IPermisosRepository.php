<?php

namespace App\Repositories;

interface IPermisosRepository
{
    public function get();
    public function getOrderedBy($property);
    public function show($id);
    // public function create($data);
    // public function update($data, $id);
    // public function delete($id);
}
