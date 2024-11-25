<?php

namespace App\Http\Controllers;

use App\AperturaCaja;
use App\Garantia;
use App\User;
use App\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function ventas()
    {
        return view("reportes.ventas");
    }
    public function ventasTiempo()
    {
        return view("reportes.ventas-tiempo");
    }
    
    public function buscarMenosVendidos(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;

        $startDate .= ' 00:00:00';
        $endDate .= ' 23:59:59';

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
 	     WHERE v.created_at >= '{$startDate}' AND v.created_at <= '{$endDate}'
 	GROUP BY vp.producto_id, vp.compra, vp.venta) t
INNER JOIN productos p ON t.id = p.id
INNER JOIN categorias c ON p.categoria_id = c.id
ORDER BY veces DESC;"));

        return response()->json($ventas);
    }
    public function buscarPorMes(Request $request)
    {
        $ventas = AperturaCaja::where("fecha_hora_cierre", ">=", $request->startDate . " 00:00:00")
            ->where("fecha_hora_cierre", "<=", $request->endDate . " 23:59:59")
            ->orderBy("created_at", "desc")
            ->get();
        return response()->json($ventas);
    }
    public function ventasTiempoJson(Request $request)
    {
        $labels = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $datasets = [[], []];
        $garantias = Garantia::pluck('venta_id')->all();
        for ($r = 1; $r < 13; $r++) {
            $from = date("Y-m-d 00:00", strtotime("{$request->year}-$r-01"));
            $to = date("Y-m-t 23:59", strtotime("{$request->year}-$r"));
            $ventas = Venta::whereBetween("created_at", [$from, $to])->whereNotIn("id", $garantias);
            $dinero = $ventas->sum("total");
            $utilidad = $ventas->sum("utilidad");

            $datasets[0][] = $dinero;
            $datasets[1][] = $utilidad;
        }
        return [
            "labels" => $labels,
            "datasets" => $datasets
        ];
    }
    public function ventasVendedorJson(Request $request)
    {
        $meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $datasets = [];
        $garantias = Garantia::pluck('venta_id')->all();
        $vendedores = User::orderBy("email")->get();
        $labels = [];
        foreach ($vendedores as $vendedor) {
            $data = [];
            for ($r = 1; $r <= 12; $r++) {
                $labels[$r - 1] = $meses[$r - 1];
                $from = date("Y-m-d 00:00", strtotime("{$request->year}-$r-01"));
                $to = date("Y-m-t 23:59", strtotime("{$request->year}-$r"));
                $ventas = Venta::whereBetween("created_at", [$from, $to])
                    ->whereNotIn("id", $garantias)
                    ->where("usuario_id", $vendedor->id)->count();
                $data[] = $ventas;
            }
            $color = _color();
            $datasets[] = [
                "label" => $vendedor->email,
                "backgroundColor" => $color,
                "borderColor" => $color,
                "data" => $data,
            ];
        }
        return [
            "labels" => $labels,
            "datasets" => $datasets
        ];
    }
    public function ventasProductoJson(Request $request)
    {
        $from = date("{$request->year}-01-01 00:00");
        $to = date("{$request->year}-12-31 23:59");
        $ventas = DB::select(DB::raw("SELECT COUNT(*) cantidad, 
                                                      p.nombre 
                                        FROM ventas_productos vp
                                        INNER JOIN productos p 
                                        ON vp.producto_id = p.id
                                        INNER JOIN ventas v 
                                        ON vp.venta_id = v.id
                                        WHERE 
                                        v.created_at BETWEEN '$from' AND '$to' AND
                                        p.categoria_id <> 6 AND 
                                        p.nombre NOT LIKE '%MICA GENERAL%'
                                        GROUP BY p.nombre 
                                        ORDER BY cantidad desc
                                        LIMIT 0, 30"));

        $labels = [];
        $datasets = [];
        foreach ($ventas as $venta) {
            $labels[] = $venta->nombre;
            $datasets[] = $venta->cantidad;
        }

        return [
            "labels" => $labels,
            "datasets" => $datasets
        ];
    }
}
