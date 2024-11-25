<?php

namespace Tests\Feature;

use App\Enums\FuenteGasto;
use App\Repositories\GastosRepository;
use App\User;
use Database\Seeders\Gastos\TestingSeeder;
use Tests\TestCase;

class GastosControllerTest extends TestCase
{
    private $_endpoint = '/gastos';
    private $_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
    }

    public function test_index_when_called_return_list_of_wastes()
    {
        $this->actingAs($this->_user);
        $this->get($this->_endpoint)
            ->assertOk()
            ->assertSee([
                'CONCEPTO 123',
                'CONCEPTO 456',
                'Fuente'
            ]);
    }

    public function test_index_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint)
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }

    // public function test_create_when_called_return_view()
    // {
    //     $this->actingAs($this->_user);

    //     $this->get($this->_endpoint . '/create')
    //         ->assertOk()
    //         ->assertSee(['Monto', 'Concepto']);
    // }

    // public function test_create_when_not_authorized_return_403_view()
    // {
    //     $this->_user->revokePermissionTo('gastos.creation');

    //     $this->actingAs($this->_user);

    //     $this->get($this->_endpoint . '/create')
    //         ->assertStatus(403)
    //         ->assertSee('No autorizado.');
    // }



    public function test_store_when_called_save_gasto_correctly_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $this->post('/caja/open', ['inicial_efe' => 1000, 'inicial_ele' => 200, 'inicial_apartados' => 1500]);

        $newWaste = [
            'monto' => fake()->numberBetween(100, 1000),
            'concepto' => fake()->text(100),
            'fuente' => FuenteGasto::Caja->value
        ];

        $this->post('/api' . $this->_endpoint, $newWaste)->assertStatus(200);

        $this->assertDatabaseHas('gastos', $newWaste);
        $this->assertDatabaseHas('log', [
            'modulo' => GastosRepository::class,
            'despues' => json_encode($newWaste)
        ]);
    }

    public function test_store_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $newWaste = [
            'monto' => fake()->numberBetween(100, 1000),
            'fuente' => FuenteGasto::MercadoPago->value,
            'concepto' => fake()->text(100)
        ];

        $this->post('/api' . $this->_endpoint, $newWaste)
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }

    public function test_store_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $newWaste = [
            'monto' => 'Expected a number',
            'concepto' => fake()->text(100),
            'fuente' => FuenteGasto::Caja->value,
        ];

        $this->post('/api' . $this->_endpoint, $newWaste)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'modulo' => GastosRepository::class,
            'antes' => json_encode($newWaste)
        ]);
        $this->assertDatabaseMissing('log', [
            'modulo' => GastosRepository::class,
            'despues' => json_encode($newWaste)
        ]);
    }

    public function test_store_when_no_valid_no_saved_return_error_message()
    {
        $this->actingAs($this->_user);

        $newWaste = [
            'monto' => 1
        ];

        $this->post('/api' . $this->_endpoint, $newWaste)
            ->assertStatus(302)
            ->assertSessionHasErrors(['concepto', 'fuente'])
            ->assertSessionHasInput('monto');
    }



    public function test_destroy_when_called_soft_deleted_correctly()
    {
        $this->actingAs($this->_user);

        $this->delete($this->_endpoint . '/3')
            ->assertStatus(302);

        $this->assertSoftDeleted('gastos', ['id' => 3]);
    }

    public function test_destroy_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->delete($this->_endpoint . '/3')
            ->assertStatus(403)
            ->assertSee('No autorizado');
    }
}
