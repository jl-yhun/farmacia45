<?php

namespace App\Repositories;

interface IRolesRepository
{
    public function get();
    public function getOrderedBy($property);
    public function show($id);
}
