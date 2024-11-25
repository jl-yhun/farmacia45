<?php

namespace App\Repositories;

use App\Helpers\LoggerBuilder;
use Spatie\Permission\Models\Permission;

class PermisosRepository implements IPermisosRepository
{
    private $_logger;
    public function __construct(LoggerBuilder $logger)
    {
        $this->_logger = $logger;
    }

    public function get()
    {
        return Permission::all();
    }

    public function getOrderedBy($property)
    {
        return Permission::orderBy($property)->get();
    }

    public function show($id)
    {
        return Permission::find($id);
    }
}
