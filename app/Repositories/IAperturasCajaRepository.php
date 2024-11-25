<?php

namespace App\Repositories;

interface IAperturasCajaRepository
{
    public function get();
    public function show($id);
    public function getCurrent();
    public function getLast();
}
