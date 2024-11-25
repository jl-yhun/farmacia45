<?php
namespace Database\Seeders;

use App\Configuracion;
use Illuminate\Database\Seeder;

class AddConfiguracionesGeneralesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Configuracion::updateOrCreate(["id" => 1], ["clave" => "ESTADO_CAJA", "valor" => "cerrada"]);
        Configuracion::updateOrCreate(["id" => 2], ["clave" => "DIAS_GARANTIA", "valor" => "7"]);
        Configuracion::updateOrCreate(["id" => 3], ["clave" => "MERCADO_PAGO_COMISION_SERVICIOS", "valor" => "3"]);
        // 3.5% + IVA = .035 * 1.16
        Configuracion::updateOrCreate(["id" => 4], ["clave" => "MERCADO_PAGO_COMISION_COBROS", "valor" => ".0406"]);
        Configuracion::updateOrCreate(["id" => 5], ["clave" => "COMISION_SERVICIOS", "valor" => "15"]);
        Configuracion::updateOrCreate(["id" => 6], ["clave" => "COMISION_RECARGAS", "valor" => "1"]);
    }
}
