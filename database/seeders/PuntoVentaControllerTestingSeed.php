<?php

namespace Database\Seeders;

use App\Apartado;
use App\Enums\MetodoPago;
use App\Enums\RecargasCompania;
use App\Recarga;
use App\Servicio;
use App\Transferencia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PuntoVentaControllerTestingSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TruncateNeededTables::class);
        $this->call(AdminAccessSeed::class);
        $this->call(UsuariosSeed::class);
        $this->call(ProductosSeed::class);
        $this->call(CategoriasSeed::class);
        $this->call(PerdidasSeed::class);
        $this->call(GastosSeed::class);
        $this->call(VentasSeed::class);
        $this->call(AddConfiguracionesGeneralesSeeder::class);

        $this->seedRecargas();
        $this->seedServicios();
        $this->seedTransferencias();
        $this->seedApartados();
    }

    private function seedRecargas()
    {
        Recarga::updateOrCreate([
            'id' => 1
        ], [
            'apertura_caja_id' => 1,
            'monto' => 10,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'compania' => RecargasCompania::Movistar->value
        ]);

        Recarga::updateOrCreate([
            'id' => 2
        ], [
            'apertura_caja_id' => 1,
            'monto' => 20,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'compania' => RecargasCompania::Movistar->value
        ]);

        Recarga::updateOrCreate([
            'id' => 3
        ], [
            'apertura_caja_id' => 1,
            'monto' => 20,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'compania' => RecargasCompania::Movistar->value
        ]);
    }

    private function seedServicios()
    {
        Servicio::updateOrCreate([
            'id' => 1
        ], [
            'apertura_caja_id' => 1,
            'monto' => 10,
            'comision' => 15,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'servicio' => 'Totalplay'
        ]);

        Servicio::updateOrCreate([
            'id' => 2
        ], [
            'apertura_caja_id' => 1,
            'monto' => 20,
            'comision' => 15,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'servicio' => 'Totalplay'
        ]);

        Servicio::updateOrCreate([
            'id' => 3
        ], [
            'apertura_caja_id' => 1,
            'monto' => 50,
            'comision' => 15,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'servicio' => 'Totalplay'
        ]);
    }

    private function seedTransferencias()
    {
        Transferencia::updateOrCreate([
            'id' => 1,
        ], [
            'monto' => 100,
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'concepto' => 'Concepto Transferencia'
        ]);
    }

    private function seedApartados() {
        Apartado::updateOrCreate([
            'id' => 1,
        ], [
            'monto' => 117,
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'concepto' => 'CONCEPTO APARTADO'
        ]);

        Apartado::updateOrCreate([
            'id' => 2,
        ], [
            'monto' => -780,
            'usuario_id' => 1,
            'apertura_caja_id' => 1,
            'concepto' => 'GASTO FROM APARTADOS'
        ]);
    }
}
