<?php

namespace App\Http\Requests\servicios;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
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
            'monto' => 'required',
            'servicio' => 'required',
            'metodo_pago' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'monto.required' => 'Debe ingresar un monto.',
            'servicio.required' => 'Debe ingresar un servicio.',
            'metodo_pago.required' => 'Debe seleccionar un método de pago.'
        ];
    }
}
