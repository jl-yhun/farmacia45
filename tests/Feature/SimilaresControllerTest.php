<?php

namespace Tests\Feature;

use App\Producto;
use App\Repositories\ProductosRepository;
use App\User;
use Database\Seeders\Similares\TestingSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SimilaresControllerTest extends TestCase
{
    private $_endpoint = '/api/similares';
    private $_user;
    private $_productos;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
        $this->_productos = Producto::all();
    }

    public function test_store_when_called_saved_correctly_log_recorded()
    {
        $this->actingAs($this->_user);
        $input = [
            fake()->numberBetween(1, 10),
            fake()->numberBetween(11, 20)
        ];

        $this->post($this->_endpoint, $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('productos_similares', [
            'base_producto_id' => $input[0],
            'similar_producto_id' => $input[1]
        ]);

        $this->assertDatabaseHas('productos_similares', [
            'base_producto_id' => $input[1],
            'similar_producto_id' => $input[0]
        ]);

        $this->assertDatabaseHas('log', [
            'link_id' => $input[0],
            'despues' => json_encode($input)
        ]);
    }

    public function test_store_when_error_return_correct_response_log_recorded()
    {
        $this->actingAs($this->_user);

        $input = [];

        $this->post($this->_endpoint, $input)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Error al agregar producto similar.',
            'antes' => json_encode($input)
        ]);
    }

    public function test_store_when_unathorized_return_correct_response()
    {
        $user = User::find(2); // no authorized
        $this->actingAs($user);

        $input = [];

        $this->post($this->_endpoint, $input)
            ->assertForbidden();
    }



    public function test_index_when_called_return_correct_result()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/1')
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJson(function (AssertableJson $json) {
                $json->etc()
                    ->has('data.0', function (AssertableJson $json) {
                        $json->etc()
                            ->where('descripcion', $this->_productos[1]->descripcion)
                            ->where('nombre', $this->_productos[1]->nombre)
                            ->where('id', $this->_productos[1]->id)
                            ->etc();
                    })
                    ->has('data.1', function (AssertableJson $json) {
                        $json->etc()
                            ->where('descripcion', $this->_productos[3]->descripcion)
                            ->where('nombre', $this->_productos[3]->nombre)
                            ->where('id', $this->_productos[3]->id)
                            ->etc();
                    });
            });
    }

    public function test_index_when_unathorized_return_correct_result()
    {
        $user = User::find(2); // Unathorized
        $this->actingAs($user);

        $this->get($this->_endpoint . '/1')
            ->assertForbidden();
    }
}
