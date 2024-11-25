<?php

namespace App\Repositories;

use App\AperturaCaja;
use App\Enums\AperturaCajaEstado;

class AperturasCajaRepository implements IAperturasCajaRepository
{
    public function __construct()
    {
    }

    public function get()
    {
        return AperturaCaja::orderBy('id', 'desc')->get();
    }

    public function show($id)
    {
        return AperturaCaja::find($id);
    }

    public function getCurrent()
    {
        return AperturaCaja::where('estado', AperturaCajaEstado::Pendiente->value)
            ->first();
    }

    public function getLast()
    {
        return AperturaCaja::orderBy('id', 'desc')->first();
    }
}
