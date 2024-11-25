<?php

namespace App\Repositories;

interface IPuntoVentaRepository
{
    public function existsAperturaCaja();
    public function getCurrentAperturaCaja();
    public function getLastAperturaCaja();
    public function closeCaja();
    public function openCaja($data);

    // public function verifyBeforeSelling($data);
    public function sell($data);
}
