<?php

namespace App\Http\Requests\Transferencias;

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
            'tipo' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'monto.required' => 'Debe ingresar un monto.',
            'concepto.required' => 'Debe ingresar un concepto.',
            'tipo.required' => 'Debe ingresar un tipo de transferencia.',
        ];
    }
}
