<?php

namespace App\Http\Controllers;

use App\MotivoGarantia;

class MotivosGarantiaController extends Controller
{
    protected $singular = "motivo_garantia";
    protected $plural = "motivos-garantias";
    protected $modelo = MotivoGarantia::class;
    protected $validatorStore = [
        "nombre" => "required"
    ];
    protected $validationMessages = [
        "nombre.required" => "El nombre es requerido."
    ];
    protected $data = [];

    public function __construct()
    {
        // parent::__construct();
        $this->middleware(["auth"]);
    }
}
