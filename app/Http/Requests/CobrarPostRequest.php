<?php

namespace App\Http\Requests;

use App\Enums\MetodoPago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CobrarPostRequest extends FormRequest
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
            'se-recibe' => [
                Rule::when(function ($input) {
                    return $input->metodo_pago == MetodoPago::Efectivo->value;
                }, 'gte:total')
            ]
        ];
    }

    public function messages()
    {
        return [
            'se-recibe.gte' => 'No alcanza para saldar la cuenta.'
        ];
    }
}
