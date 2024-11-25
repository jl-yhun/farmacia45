<?php

namespace App\Http\Controllers;

class HerramientasController extends Controller
{
    public function conteo()
    {
        $valores = [
            'billetes' => [1000, 500, 200, 100, 50, 20],
            'monedas' => [20, 10, 5, 2, 1],
            'centavos' => [.5, .20, .10]
        ];
        return view('punto-venta.conteo', compact('valores'));
    }
}
