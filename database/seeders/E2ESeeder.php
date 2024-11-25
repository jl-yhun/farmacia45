<?php

namespace Database\Seeders;

use App\Categoria;
use App\Enums\MetodoPago;
use App\Gasto;
use App\Perdida;
use App\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class E2ESeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->truncateTables();
        $this->call(AdminAccessSeed::class);
        $this->call(AddConfiguracionesGeneralesSeeder::class);
        $this->call(ProductosSeed::class);

        $this->seedUsuarios();
        $this->seedCategorias();
        $this->seedPerdidas();
        $this->seedGastos();
    }

    private function truncateTables()
    {
        DB::table('aperturas_caja')->truncate();
        DB::table('users')->truncate();
        DB::table('categorias')->truncate();
        DB::table('perdidas')->truncate();
        DB::table('gastos')->truncate();
        DB::table('productos')->truncate();
        DB::table('productos_proveedores')->truncate();
        DB::table('productos_similares')->truncate();
    }

    private function seedUsuarios()
    {
        $user = User::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'user1',
                'email' => 'user1',
                'password' => 'password1'
            ]
        );

        $user->givePermissionTo('categorias.view');
        $user->givePermissionTo('perdidas.view');
        $user->givePermissionTo('gastos.view');

        User::updateOrCreate(
            ['id'     => 3],
            [
                'name' => 'user2',
                'email' => 'user2',
                'password' => 'password2'
            ]
        );

        User::withTrashed()->updateOrCreate(
            ['id'     => 4],
            [
                'name' => 'user3',
                'email' => 'user3',
                'deleted_at' => null,
                'password' => 'password3'
            ]
        );
    }

    private function seedCategorias()
    {
        Categoria::updateOrCreate(
            ['id' => 1],
            [
                'nombre' => 'Categoría xyz',
                'admite' => 'NINGUNO',
                'tasa_iva' => .16
            ]
        );

        Categoria::updateOrCreate(
            ['id'     => 2],
            [
                'nombre' => 'Categoría abc',
                'admite' => 'NINGUNO',
                'tasa_iva' => 0
            ]
        );

        Categoria::withTrashed()->updateOrCreate(
            ['id'     => 3],
            [
                'deleted_at' => null,
                'nombre' => 'Categoría abc-d',
                'admite' => 'NINGUNO',
                'tasa_iva' => .16
            ]
        );

        for ($i = 4; $i < 51; $i++) {
            Categoria::withTrashed()->updateOrCreate(
                ['id'     => $i],
                [
                    'deleted_at' => null,
                    'nombre' => 'Categoría ' . $i,
                    'admite' => 'NINGUNO',
                    'tasa_iva' => 0
                ]
            );
        }
    }

    private function seedPerdidas()
    {
        for ($i = 1; $i < 51; $i++) {
            Perdida::updateOrCreate(
                ['id' => $i],
                [
                    'producto_id' => 1,
                    'usuario_id' => 1,
                    'garantia_id' => 1,
                    'motivo' => 'Motivo pérdida ' . $i
                ]
            );
        }
    }

    private function seedGastos()
    {
        for ($i = 1; $i < 51; $i++) {
            Gasto::updateOrCreate(
                ['id' => $i],
                [
                    'apertura_caja_id' => 1,
                    'usuario_id' => 1,
                    'monto' => 1,
                    'concepto' => 'GASTO ' . $i,
                    'fuente' => 'Caja'
                ]
            );
        }
    }
}
