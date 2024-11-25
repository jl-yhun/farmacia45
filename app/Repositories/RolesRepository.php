<?php

namespace App\Repositories;

use App\Helpers\LoggerBuilder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesRepository implements IRolesRepository
{
    private $_logger;
    public function __construct(LoggerBuilder $logger)
    {
        $this->_logger = $logger;
    }

    public function get()
    {
        return Role::all();
    }

    public function getOrderedBy($property)
    {
        return Role::orderBy($property)->get();
    }

    public function show($id)
    {
        return Role::find($id);
    }
}
