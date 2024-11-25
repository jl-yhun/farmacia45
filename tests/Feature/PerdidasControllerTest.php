<?php

namespace Tests\Feature;

use App\Producto;
use App\Repositories\PerdidasRepository;
use App\User;
use Database\Seeders\Perdidas\TestingSeeder;
use Tests\TestCase;

class PerdidasControllerTest extends TestCase
{
    private $_endpoint = '/perdidas';
    private $_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
    }

    public function test_index_when_called_return_list_of_losses()
    {
        $this->actingAs($this->_user);
        $this->get($this->_endpoint)
            ->assertOk()
            ->assertSee(['MOTIVO 123', 'MOTIVO 456']);
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
            ->assertOk()
            ->assertSee(['Producto', 'Motivo'])
            ->assertViewHas('productos');
    }

    public function test_create_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint . '/create')
            ->assertStatus(403)
            ->assertSee('No autorizado.');
    }

    public function test_store_when_called_save_perdida_correctly_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $newLoss = [
            'producto_id' => 1,
            'usuario_id' => 1,
            'garantia_id' => 1,
            'motivo' => fake()->text(50)
        ];

        $this->post($this->_endpoint, $newLoss)->assertStatus(200);

        $producto = Producto::find(1);

        $this->assertDatabaseHas('perdidas', $newLoss);
        $this->assertDatabaseHas('log', [
            'modulo' => PerdidasRepository::class,
            'despues' => json_encode($newLoss)
        ]);
        $this->assertEquals(99, $producto->stock);
    }

    public function test_store_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $newLoss = [
            'producto_id' => 1,
            'usuario_id' => 1,
            'garantia_id' => 1,
            'motivo' => fake()->text(50)
        ];

        $this->post($this->_endpoint, $newLoss)
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }

    public function test_store_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $newLoss = [
            'producto_id' => 1,
            'motivo' => fake()->text(50)
        ];

        $this->post($this->_endpoint, $newLoss)
            ->assertStatus(302)
            ->assertSessionHasErrors('general');

        $this->assertDatabaseHas('log', [
            'modulo' => PerdidasRepository::class,
            'antes' => json_encode($newLoss)
        ]);
        $this->assertDatabaseMissing('log', [
            'modulo' => PerdidasRepository::class,
            'despues' => json_encode($newLoss)
        ]);
    }

    public function test_store_when_no_valid_no_saved_return_error_message()
    {
        $this->actingAs($this->_user);

        $newLoss = [
            'producto_id' => 1,
            'usuario_id' => 1,
            'garantia_id' => 1
        ];

        $this->post($this->_endpoint, $newLoss)
            ->assertStatus(302)
            ->assertSessionHasErrors('motivo')
            ->assertSessionHasInput('producto_id');
    }

    public function test_store_when_no_stock_return_error_and_log_saved()
    {
        $this->actingAs($this->_user);

        $newLoss = [
            'producto_id' => 5,
            'usuario_id' => 1,
            'garantia_id' => 1,
            'motivo' => fake()->text(50)
        ];

        $this->post($this->_endpoint, $newLoss)
            ->assertStatus(302)
            ->assertSessionHasErrors('general');

        $this->assertDatabaseHas('log', [
            'modulo' => PerdidasRepository::class,
            'descripcion' => 'Stock no puede ser menor a 0.',
            'antes' => json_encode($newLoss)
        ]);
        $this->assertDatabaseMissing('log', [
            'modulo' => PerdidasRepository::class,
            'despues' => json_encode($newLoss)
        ]);
    }
}
