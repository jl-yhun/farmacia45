<?php

namespace App\Http\Requests\Productos;

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
            'caducidad' => 'nullable|date',
            'nombre' => 'required',
            'stock' => 'required|min:1',
            'categoria_id' => 'required',
            'compra' => 'required',
            'venta' => 'required|gt:compra',
            'codigo_barras' => 'unique:productos,codigo_barras',
            'min_stock' => 'required',
            'max_stock' => 'required|min:1',
            'unidades_paquete' => 'exclude_if:isGranel,0|required_if:isGranel,1|numeric|min:1'
        ];
    }

    public function messages()
    {
        return [
            'nombre.required' => 'El nombre es requerido.',
            'caducidad.date' => 'El campo caducidad debe ser una fecha válida.',
            'stock.required' => 'El stock es requerido.',
            'stock.min' => 'El stock debe ser al menos :min.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'compra.required' => 'El precio de compra es requerido.',
            'venta.required' => 'El precio de venta es requerido.',
            'venta.gt' => 'El precio de venta debe ser mayor al de compra.',
            'codigo_barras.unique' => 'Código de barras duplicado.',
            'min_stock.required' => 'El stock mínimo es requerido.',
            'max_stock.required' => 'El stock máximo es requerido.',
            'max_stock.min' => 'El stock máximo debe ser mayor a 1.',
            'unidades_paquete.required' => 'Las unidades por paquete son requeridas.',
            'unidades_paquete.min' => 'Las unidades por paquete NO pueden ser 0.',
        ];
    }
}
