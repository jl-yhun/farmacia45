<?php

namespace App\Http\Requests\ProductosProveedores;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class PutRequest extends FormRequest
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
            'proveedor_id' => [
                'required',
                function ($attr, $value, $fail) {
                    $exists = DB::table('productos_proveedores')
                        ->where('proveedor_id', $this->input('proveedor_id'))
                        ->where('producto_id', $this->route('id'))
                        ->where('proveedor_id', '!=', $this->route('proveedor_id'))
                        ->get()
                        ->count() > 0;

                    if ($exists)
                        $fail('Este registro ya existe.');
                }
            ],
            'codigo' => 'required',
            'disponible' => 'required',
            'precio' => 'required',
            'default' => [
                function ($attr, $value, $fail) {
                    if ($value) {
                        $exists = DB::table('productos_proveedores')
                            ->where('producto_id', $this->route('id'))
                            ->where('default', true)
                            ->where('proveedor_id', '!=', $this->input('proveedor_id'))
                            ->get()
                            ->count() > 0;

                        if ($exists)
                            $fail('Sólo se puede seleccionar un proveedor por defecto.');
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'proveedor_id.required' => 'Debe seleccionar un proveedor.',
            'codigo.required' => 'Debe ingresar un código.',
            'disponible.required' => 'Debe seleccionar si el producto está disponible con este proveedor.',
            'precio.required' => 'Debe ingresar un precio.'
        ];
    }
}
