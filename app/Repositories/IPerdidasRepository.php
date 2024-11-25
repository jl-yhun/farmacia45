<?php

namespace App\Repositories;

interface IPerdidasRepository
{
    public function get();
    public function show($id);
    public function create($data);
    public function update($data, $id);
    public function delete($id);
}
