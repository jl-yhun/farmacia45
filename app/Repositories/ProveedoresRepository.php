<?php

namespace App\Repositories;

use App\Helpers\LoggerBuilder;
use App\Proveedor;

class ProveedoresRepository implements IProveedoresRepository
{
    private $_logger;

    public function __construct(LoggerBuilder $logger)
    {
        $this->_logger = $logger;
    }

    public function get()
    {
        return Proveedor::all();
    }
}
