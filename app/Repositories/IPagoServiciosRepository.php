<?php

namespace App\Repositories;

interface IPagoServiciosRepository
{
    public function createRecarga($data);
    public function createServicio($data);
}
