<?php

namespace App\Repositories;

interface IConfiguracionRepository
{
    public function set($key, $value);
    public function get($key);
}
