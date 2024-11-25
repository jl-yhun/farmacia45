<?php

namespace App\Http\Requests\Gastos;

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
            'concepto' => 'required',
            'fuente' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'monto.required' => 'Debe poner un monto.',
            'concepto.required' => 'Escriba un concepto del gasto.',
            'fuente.required' => 'Debe seleccionar una fuente del gasto.'
        ];
    }
}
