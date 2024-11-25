<?php

namespace Database\Seeders;

use App\AperturaCaja;
use App\Enums\AperturaCajaEstado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AperturasCajaControllerTestSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminAccessSeed::class);
        $this->call(UsuariosSeed::class);

        DB::table('aperturas_caja')->truncate();
        $this->seedAperturasCaja();
    }

    private function seedAperturasCaja()
    {
        AperturaCaja::updateOrCreate([
            'id' => 1
        ], [
            'inicial_efe' => fake()->numberBetween(10, 100),
            'inicial_ele' => fake()->numberBetween(10, 100),
            'inicial_apartados' => fake()->numberBetween(10, 100),
            'inicial_recargas_servicios' => fake()->numberBetween(10, 100),
            'ventas_efe' => fake()->numberBetween(10, 100),
            'ventas_ele' => fake()->numberBetween(10, 100),
            'utilidades' => fake()->numberBetween(10, 100),
            'gastos_efe' => fake()->numberBetween(10, 100),
            'gastos_ele' => fake()->numberBetween(10, 100),
            'servicios_recargas_efe' => fake()->numberBetween(10, 100),
            'servicios_recargas_ele' => fake()->numberBetween(10, 100),
            'total' => fake()->numberBetween(10, 100),
            'subtotal_efe' => fake()->numberBetween(10, 100),
            'subtotal_ele' => fake()->numberBetween(10, 100),
            'subtotal_apartados' => fake()->numberBetween(10, 100),
            'fecha_hora_cierre' => fake()->date(max: '2025-01-01'),
            'estado' => AperturaCajaEstado::Concluido->value,
            'observaciones' => 'Apertura caja 1',
        ]);

        AperturaCaja::updateOrCreate([
            'id' => 2
        ], [
            'inicial_efe' => fake()->numberBetween(10, 100),
            'inicial_ele' => fake()->numberBetween(10, 100),
            'inicial_apartados' => fake()->numberBetween(10, 100),
            'inicial_recargas_servicios' => fake()->numberBetween(10, 100),
            'ventas_efe' => fake()->numberBetween(10, 100),
            'ventas_ele' => fake()->numberBetween(10, 100),
            'utilidades' => fake()->numberBetween(10, 100),
            'gastos_efe' => fake()->numberBetween(10, 100),
            'gastos_ele' => fake()->numberBetween(10, 100),
            'servicios_recargas_efe' => fake()->numberBetween(10, 100),
            'servicios_recargas_ele' => fake()->numberBetween(10, 100),
            'total' => fake()->numberBetween(10, 100),
            'subtotal_efe' => fake()->numberBetween(10, 100),
            'subtotal_ele' => fake()->numberBetween(10, 100),
            'subtotal_apartados' => fake()->numberBetween(10, 100),
            'fecha_hora_cierre' => fake()->date(max: '2025-01-01'),
            'estado' => AperturaCajaEstado::Concluido->value,
            'observaciones' => 'Apertura caja 2',
        ]);

        AperturaCaja::updateOrCreate([
            'id' => 3
        ], [
            'inicial_efe' => fake()->numberBetween(10, 100),
            'inicial_ele' => fake()->numberBetween(10, 100),
            'inicial_apartados' => fake()->numberBetween(10, 100),
            'inicial_recargas_servicios' => fake()->numberBetween(10, 100),
            'estado' => AperturaCajaEstado::Pendiente->value,
            'observaciones' => 'Apertura caja 3',
        ]);
    }
}
