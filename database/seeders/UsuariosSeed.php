<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UsuariosSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::updateOrCreate(
            ['id' => 2],
            [
                'name' => 'user1',
                'email' => 'user1',
                'password' => fake()->password()
            ]
        );

        $user->givePermissionTo('productos.update');
        $user->givePermissionTo('apartados.view');

        User::updateOrCreate(
            ['id'     => 3],
            [
                'name' => 'user2',
                'email' => 'user2',
                'password' => fake()->password()
            ]
        );

        User::withTrashed()->updateOrCreate(
            ['id'     => 4],
            [
                'name' => 'user3',
                'email' => 'user3',
                'deleted_at' => null,
                'password' => fake()->password()
            ]
        );
    }
}
