<?php

namespace App\Http\Requests\Perdidas;

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
            "producto_id" => "required",
            "motivo" => "required"
        ];
    }
    public function messages()
    {
        return [
            "producto_id.required" => "Seleccione un producto.",
            "motivo.required" => "Escriba el motivo de la pérdida.",
        ];
    }
}
