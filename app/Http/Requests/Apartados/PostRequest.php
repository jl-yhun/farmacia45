<?php

namespace App\Http\Requests\Apartados;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'concepto' => 'required',
            'monto' => [
                'required',
                Rule::notIn([0])
            ]
        ];
    }

    public function messages()
    {
        return [
            'concepto.required' => 'Debe poner un concepto.',
            'monto.required' => 'Debe poner un monto.',
            'monto.not_in' => 'Debe poner un monto.'
        ];
    }
}
