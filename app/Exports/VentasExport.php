<?php

namespace App\Exports;

use App\Venta;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasExport implements FromArray, WithHeadings
{
    private $fechaFinal;
    private $fechaInicial;

    public function __construct($fechaInicial, $fechaFinal)
    {
        $this->fechaInicial = $fechaInicial;
        $this->fechaFinal = $fechaFinal;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $ventas = Venta::where('created_at', '>=', $this->fechaInicial . ' 00:00:00')
            ->where('created_at', '<=', $this->fechaFinal . ' 23:59:59')->get();

        $result = [];
        foreach ($ventas as $venta) {
            if (!$venta->garantia_aplicada) {
                array_push($result, [
                    "folio" => $venta->id,
                    "usuario" => $venta->usuario->email,
                    "metodo" => $venta->metodo_pago,
                    "total" => $venta->total,
                    "utilidad" => $venta->utilidad,
                    "fecha" => $venta->created_at->format("Y-m-d"),
                    "hora" => $venta->created_at->format("H:i"),
                ]);
            }
        }
        return $result;
    }
    public function headings(): array
    {
        return ["folio", "usuario", "metodo", "total", "utilidad", "fecha", "hora"];
    }
}
