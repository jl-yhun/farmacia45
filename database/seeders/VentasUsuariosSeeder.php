<?php
namespace Database\Seeders;

use App\User;
use App\Venta;
use Illuminate\Database\Seeder;

class VentasUsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usuario = User::where("email", "giokyper@gmail.com")->first();
        $ventas = Venta::all();
        foreach($ventas as $venta){
            $venta->usuario_id = $usuario->id;
            $venta->save();
        }
    }
}
