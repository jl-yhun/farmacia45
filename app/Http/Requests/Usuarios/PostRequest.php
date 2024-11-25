<?php

namespace App\Http\Requests\Usuarios;

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
            "name" => "required",
            "email" => "required",
            "password" => "required",
            "role_id" => "required"
        ];
    }

    public function messages()
    {
        return [
            "name.required" => "El nombre es requerido.",
            "email.required" => "El usuario es requerido.",
            "password.required" => "La contraseña es requerida.",
            "role_id.required" => "Debe seleccionar un rol.",
        ];
    }
}
