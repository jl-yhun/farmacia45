<?php

namespace Database\Seeders;

use App\Enums\MetodoPago;
use App\Venta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VentasSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $venta1 = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'total' => 400,
            'denominacion' => 500,
            'cambio' => 100,
            'utilidad' => 140
        ]);

        $venta1->productos()->attach(1, [
            'cantidad' => 1,
            'venta' => 150,
            'compra' => 100
        ]);
        $venta1->productos()->attach(2, [
            'cantidad' => 2,
            'venta' => 125,
            'compra' => 80
        ]);

        $venta2 = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::TarjetaDebito->value,
            'total' => 150,
            'denominacion' => 500,
            'cambio' => 350,
            'utilidad' => 50
        ]);
        $venta2->productos()->attach(1, [
            'cantidad' => 1,
            'venta' => 150,
            'compra' => 100
        ]);

        $venta3 = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'total' => 600,
            'denominacion' => 600,
            'cambio' => 0,
            'utilidad' => 190
        ]);

        $venta3->productos()->attach(1, [
            'cantidad' => 1,
            'venta' => 150,
            'compra' => 100
        ]);
        $venta3->productos()->attach(2, [
            'cantidad' => 2,
            'venta' => 125,
            'compra' => 80
        ]);
        $venta3->productos()->attach(3, [
            'cantidad' => 2,
            'venta' => 100,
            'compra' => 75
        ]);

        $venta4 = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::TarjetaDebito->value,
            'total' => 325,
            'denominacion' => 350,
            'cambio' => 25,
            'utilidad' => 95
        ]);

        $venta4->productos()->attach(2, [
            'cantidad' => 1,
            'venta' => 125,
            'compra' => 80
        ]);
        $venta4->productos()->attach(3, [
            'cantidad' => 2,
            'venta' => 100,
            'compra' => 75
        ]);

        $venta5 = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'total' => 350,
            'denominacion' => 400,
            'cambio' => 50,
            'utilidad' => 100
        ]);

        $venta5->productos()->attach(1, [
            'cantidad' => 1,
            'venta' => 150,
            'compra' => 10
        ]);
        $venta5->productos()->attach(3, [
            'cantidad' => 2,
            'venta' => 100,
            'compra' => 75
        ]);

        $ventaCancelable = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'total' => 225,
            'denominacion' => 225,
            'cambio' => 0,
            'utilidad' => 70
        ]);

        $ventaCancelable->productos()->attach(2, [
            'cantidad' => 1,
            'venta' => 125,
            'compra' => 80
        ]);
        $ventaCancelable->productos()->attach(3, [
            'cantidad' => 1,
            'venta' => 100,
            'compra' => 75
        ]);

        $ventaCancelable->garantias()->create([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'tipo' => 'CANCELACIÓN',
            'perdida' => 0,
            'diferencia' => 0
        ]);

        $venta6 = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'total' => 350,
            'denominacion' => 400,
            'cambio' => 50,
            'utilidad' => 100
        ]);

        $venta6->productos()->attach(1, [
            'cantidad' => 1,
            'venta' => 150,
            'compra' => 10
        ]);
        $venta6->productos()->attach(3, [
            'cantidad' => 2,
            'venta' => 100,
            'compra' => 75
        ]);

        $venta7 = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'total' => 200,
            'denominacion' => 200,
            'cambio' => 0,
            'utilidad' => 100
        ]);

        $venta7->productos()->attach(6, [
            'cantidad' => 1,
            'venta' => 200,
            'compra' => 100
        ]);

        $venta8 = Venta::updateOrCreate([
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'metodo_pago' => MetodoPago::Transferencia->value,
            'total' => 200,
            'denominacion' => 0,
            'cambio' => 0,
            'utilidad' => 100
        ]);

        $venta8->productos()->attach(6, [
            'cantidad' => 1,
            'venta' => 200,
            'compra' => 100
        ]);
    }
}
