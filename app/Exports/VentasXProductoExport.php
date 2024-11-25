<?php

namespace App\Exports;

use App\Venta;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasXProductoExport implements FromArray, WithHeadings
{
    private $fechaFinal;
    private $fechaInicial;

    public function __construct($fechaInicial, $fechaFinal)
    {
        $this->fechaInicial = $fechaInicial . " 00:00:00";
        $this->fechaFinal = $fechaFinal . " 23:59:59";
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        $ventas = DB::select(DB::raw("
        SELECT t.id, 
	   p.nombre, 
	   c.nombre categoria, 
	   p.compra, 
	   p.venta, 
	   t.veces, 
	   t.veces*t.compra compra_total, 
	   t.veces*t.venta venta_total,
	   (t.veces*t.venta) - (t.veces*t.compra) utilidad
  FROM (SELECT vp.producto_id id, 
  			   SUM(vp.cantidad) veces, 
  			   vp.compra, 
  			   vp.venta 
  		  FROM ventas_productos vp
	INNER JOIN ventas v ON vp.venta_id = v.id
 	     WHERE v.created_at >= '{$this->fechaInicial}' AND v.created_at <= '{$this->fechaFinal}'
 	GROUP BY vp.producto_id, vp.compra, vp.venta) t
INNER JOIN productos p ON t.id = p.id
INNER JOIN categorias c ON p.categoria_id = c.id
ORDER BY veces DESC;"));

        $result = [];
        foreach ($ventas as $venta) {
            array_push($result, [
                "folio" => $venta->id,
                "producto" => $venta->nombre,
                "categoria" => $venta->categoria,
                "unidades_vendidas" => $venta->veces,
                "compra" => $venta->compra,
                "venta" => $venta->venta,
                "compra_total" => $venta->compra_total,
                "venta_total" => $venta->venta_total,
                "utilidades" => $venta->utilidad,
            ]);
        }
        return $result;
    }
    public function headings(): array
    {
        return ["folio", "producto", "categoria", "unidades_vendidas", "compra", "venta", "compra_total", "venta_total", "utilidades"];
    }
}
