<?php

namespace App\Http\Requests\OrdenesCompra\Items;

use Illuminate\Foundation\Http\FormRequest;

class PatchRequest extends FormRequest
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
            'cantidad' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'cantidad.required' => 'Debe ingresar una cantidad.'
        ];
    }
}
