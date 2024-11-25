<?php

namespace Tests\Feature;

use App\Repositories\UsuariosRepository;
use App\User;
use Database\Seeders\Usuarios\TestingSeeder;
use Tests\TestCase;

class UsuariosControllerTest extends TestCase
{
    private $_endpoint = '/usuarios';
    private $_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
    }

    public function test_index_when_called_return_list_of_users()
    {
        $this->actingAs($this->_user);
        $response = $this->get($this->_endpoint);

        $response->assertStatus(200);
        $response->assertSee('user1');
        $response->assertSee('user2');
        $response->assertSee('admin');
    }

    public function test_index_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint)
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }

    public function test_create_when_called_return_view()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/create')
            ->assertStatus(200)
            ->assertSee('Nombre')
            ->assertSee('Contraseña');
    }

    public function test_create_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint . '/create')
            ->assertStatus(403)
            ->assertSee('No autorizado.');
    }

    public function test_store_when_called_save_user_correctly_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $userEmail = fake()->userName();
        $newUser = [
            'name' => $userEmail,
            'email' => $userEmail,
            'password' => fake()->password(),
            'role_id' => 1
        ];

        $this->post($this->_endpoint, $newUser)->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'name' => $newUser['name'],
            'email' => $newUser['email'],
        ]);
        $this->assertDatabaseHas('log', [
            'modulo' => UsuariosRepository::class,
            'despues' => json_encode([
                'name' => $newUser['name'],
                'email' => $newUser['email'],
                'role_id' => $newUser['role_id']
            ])
        ]);
    }

    public function test_store_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $userEmail = fake()->userName();
        $newUser = [
            'name' => $userEmail,
            'email' => $userEmail,
            'password' => fake()->password(),
            'role_id' => 1
        ];

        $this->post($this->_endpoint, $newUser)
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }

    public function test_store_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $newUser = [
            'name' => 'user1',
            'email' => 'user1',
            'password' => fake()->password(),
            'role_id' => 1
        ];

        $this->post($this->_endpoint, $newUser)
            ->assertStatus(302)
            ->assertSessionHasErrors('general');

        $this->assertDatabaseHas('log', [
            'modulo' => UsuariosRepository::class,
            'antes' => json_encode([
                'name' => $newUser['name'],
                'email' => $newUser['email'],
                'role_id' => $newUser['role_id']
            ])
        ]);
    }

    public function test_store_when_no_valid_no_saved_return_error_message()
    {
        $this->actingAs($this->_user);

        $userEmail = fake()->userName();
        $newUser = [
            'email' => $userEmail,
            'password' => fake()->password()
        ];

        $this->post($this->_endpoint, $newUser)
            ->assertStatus(302)
            ->assertSessionHasErrors(['name', 'role_id'])
            ->assertSessionHasInput('email');
    }

    public function test_edit_when_called_return_view()
    {
        $this->actingAs($this->_user);

        $usuario = User::find(1);

        $this->get($this->_endpoint . '/1/edit')
            ->assertOk()
            ->assertSee([$usuario->name, $usuario->email])
            ->assertViewHas('roles');
    }

    public function test_edit_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint . '/1/edit')
            ->assertStatus(403)
            ->assertSee('No autorizado.');
    }

    public function test_update_when_called_save_user_correctly_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $userEmail = fake()->userName();
        $updatedUser = [
            'name' => $userEmail,
            'email' => $userEmail,
            'password' => fake()->password(),
            'role_id' => 1
        ];

        $this->put($this->_endpoint . '/4', $updatedUser)
            ->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'name' => $updatedUser['name'],
            'email' => $updatedUser['email']
        ]);
        $this->assertDatabaseHas('log', [
            'modulo' => UsuariosRepository::class,
            'despues' => json_encode([
                'name' => $updatedUser['name'],
                'email' => $updatedUser['email'],
                'role_id' => $updatedUser['role_id']
            ])
        ]);
    }

    public function test_update_when_password_empty_save_user_correctly_except_password()
    {
        $this->actingAs($this->_user);

        $userEmail = fake()->userName();
        $updatedUser = [
            'name' => $userEmail,
            'email' => $userEmail,
            'password' => '',
            'role_id' => 1
        ];

        $this->put($this->_endpoint . '/4', $updatedUser)
            ->assertStatus(200);


        $this->assertDatabaseMissing('log', [
            'modulo' => UsuariosRepository::class,
            'link_id' => 3,
            'descripcion' => 'Se cambió contraseña de usuario ' . $userEmail
        ]);
    }

    public function test_update_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $userEmail = fake()->userName();
        $updatedUser = [
            'name' => $userEmail,
            'email' => $userEmail,
            'password' => '',
            'role_id' => 1
        ];

        $this->put($this->_endpoint . '/4', $updatedUser)
            ->assertStatus(403)
            ->assertSee('No autorizado.');
    }

    public function test_update_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $updatedUser = [
            'name' => 'admin', // Duplicated
            'email' => 'admin',
            'password' => '',
            'role_id' => 1
        ];

        $this->put($this->_endpoint . '/4', $updatedUser)
            ->assertStatus(302)
            ->assertSessionHasErrors('general');

        $this->assertDatabaseHas('log', [
            'modulo' => UsuariosRepository::class,
            'tipo' => 'error',
            'despues' => json_encode([
                'name' => $updatedUser['name'],
                'email' => $updatedUser['email'],
                'role_id' => $updatedUser['role_id']
            ])
        ]);
        $this->assertDatabaseMissing('log', [
            'modulo' => UsuariosRepository::class,
            'antes' => json_encode([
                'name' => $updatedUser['name'],
                'email' => $updatedUser['email']
            ])
        ]);
    }

    public function test_update_when_invalid_return_error_message()
    {
        $this->actingAs($this->_user);

        $userEmail = fake()->userName();
        $updatedUser = [
            'name' => $userEmail,
            'password' => ''
        ];

        $this->put($this->_endpoint . '/4', $updatedUser)
            ->assertStatus(302)
            ->assertSessionHasErrors('email')
            ->assertSessionHasErrors('role_id')
            ->assertSessionHasInput('name');
    }

    public function test_destroy_when_called_soft_deleted_correctly()
    {
        $this->actingAs($this->_user);

        $this->delete($this->_endpoint . '/4')
            ->assertStatus(302);

        $this->assertSoftDeleted('users', ['id' => 4]);
    }

    public function test_destroy_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->delete($this->_endpoint . '/4')
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }
}
