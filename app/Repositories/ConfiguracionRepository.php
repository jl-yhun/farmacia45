<?php

namespace App\Repositories;

use App\Configuracion;

class ConfiguracionRepository implements IConfiguracionRepository
{
    public function set($key, $value)
    {
        $conf = Configuracion::where('clave', $key)->first();
        $conf->valor = $value;
        $conf->save();
    }

    public function get($key)
    {
        return Configuracion::where('clave', $key)->first()->valor ?? '';
    }
}
