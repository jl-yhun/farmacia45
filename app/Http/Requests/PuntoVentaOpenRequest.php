<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PuntoVentaOpenRequest extends FormRequest
{
    protected $redirectRoute = 'punto-venta.opening';
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'inicial_efe' => 'required|gt:0',
            'inicial_ele' => 'required',
            'inicial_apartados' => 'required',
            'inicial_recargas_servicios' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'inicial_efe.required' => 'Debe ingresar una cantidad de efectivo inicial.',
            'inicial_efe.gt' => 'Debe ingresar una cantidad de efectivo inicial.',
            'inicial_ele.required' => 'Debe ingresar una cantidad de dinero electrónico inicial.',
            'inicial_apartados.required' => 'Debe ingresar una cantidad de apartados.',
            'inicial_recargas_servicios.required' => 'Debe ingresar una cantidad de recargas/servicios.'
        ];
    }
}
