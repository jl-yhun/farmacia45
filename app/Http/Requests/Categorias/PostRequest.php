<?php

namespace App\Http\Requests\Categorias;

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
            'nombre' => 'required',
            'tasa_iva' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido.',
            'tasa_iva.required' => 'La tasa del IVA es requerido.'
        ];
    }
}
