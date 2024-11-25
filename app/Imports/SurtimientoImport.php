<?php

namespace App\Imports;

use App\Producto;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SurtimientoImport implements ToModel, WithHeadingRow
{
    public $data = [];
    public function model(array $row)
    {
        $prod = Producto::find($row["folio"]);

        $item = [
            "folio" => $row["folio"],
            "nombre" => $row["nombre"],
            "compra" => $row["compra"] ?? -1,
            "venta" => $row["venta"] ?? -1,
            "cantidad" => $row["cantidad"],
            "stock" => $row["cantidad"]
        ];
        if($prod){
            $item["nombre"] = $prod->nombre;
            $item["stock"] += $prod->stock;
        }
        array_push($this->data, $item);
    }
}
