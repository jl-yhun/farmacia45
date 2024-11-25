<?php

namespace Tests\Feature;

use App\Inventario;
use App\Producto;
use App\User;
use Database\Seeders\InventarioControllerTestSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class InventarioControllerTest extends TestCase
{
    private $_endpoint = '/api/inventario';
    private $_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(InventarioControllerTestSeeder::class);
        $this->_user = User::find(1);
    }

    public function test_store_when_called_register_correctly_and_log_saved()
    {
        $this->actingAs($this->_user);
        $producto = $this->showProductByCode('003');

        $input = [
            'codigo_barras' => $producto->codigo_barras
        ];
        $this->post($this->_endpoint, $input)->assertOk();

        $this->assertDatabaseHas('inventarios', [
            'producto_id' => $producto->id,
            'cantidad' => 1,
            'compra' => 75,
            'venta' => 100
        ]);

        $this->assertDatabaseMissing('log', [
            'descripcion' => 'Registro de inventario creado exitosamente.',
            'despues' => json_encode($input)
        ]);
    }

    public function test_store_when_product_not_registered_show_correct_message_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $input = [
            'codigo_barras' => 'ABC-DEF'
        ];
        $this->post($this->_endpoint, $input)
            ->assertOk()
            ->assertJsonPath('estado', false)
            ->assertJsonPath('data', 'No existe el producto.');

        $this->assertDatabaseMissing('inventarios', [
            'cantidad' => 1,
            'compra' => 0,
            'venta' => 0
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Producto no existe.',
            'antes' => json_encode($input)
        ]);
    }

    public function test_store_when_exists_update_correctly_and_log_saved()
    {
        $this->actingAs($this->_user);
        $producto = $this->showProductByCode('001');

        $input = [
            'codigo_barras' => $producto->codigo_barras
        ];
        $this->post($this->_endpoint, $input)->assertOk();

        $this->assertDatabaseHas('inventarios', [
            'producto_id' => $producto->id,
            'cantidad' => 101
        ]);
    }

    public function test_store_when_invalid_return_message()
    {
        $this->actingAs($this->_user);

        $input = [];

        $this->post($this->_endpoint, $input)
            ->assertInvalid()
            ->assertSessionHasErrors('codigo_barras');
    }



    public function test_diff_when_called_return_correct_result()
    {

        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/diff')
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJson(function (AssertableJson $json) {
                $json->etc()
                    ->has('data.diferencias.1', function ($okJson) {
                        $okJson
                            ->where('stock', '0')
                            ->where('venta', '+10')
                            ->where('compra', '0')
                            ->etc();
                    })
                    ->has('data.diferencias.4', function ($okJson) {
                        $okJson
                            ->where('stock', '+4')
                            ->where('venta', '0')
                            ->where('compra', '0')
                            ->etc();
                    })
                    ->has('data.diferencias.5', function ($okJson) {
                        $okJson
                            ->where('stock', '+1')
                            ->where('venta', '0')
                            ->where('compra', '0')
                            ->etc();
                    })
                    ->has('data.inexistencias.3', function ($overAddedJson) {
                        $overAddedJson
                            ->where('stock', 300)
                            ->where('compra', '75.00')
                            ->where('venta', '100.00')
                            ->etc();
                    })->etc();
            });
    }


    public function test_index_when_called_return_list_of_current_products_searched()
    {

        $this->actingAs($this->_user);

        $this->get($this->_endpoint)
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->etc()
                    ->has('data.0',  function (AssertableJson $missingJson) {
                        $missingJson
                            ->etc()
                            ->has('producto.codigo_barras')
                            ->has('producto.nombre')
                            ->has('producto.descripcion')
                            ->where('producto_id', 1)
                            ->where('cantidad', 100)
                            ->where('compra', '100.00')
                            ->where('venta', '160.00')
                            ->etc();
                    })->has('data.1', function ($notAddedJson) {
                        $notAddedJson
                            ->etc()
                            ->has('producto.codigo_barras')
                            ->has('producto.nombre')
                            ->has('producto.descripcion')
                            ->where('producto_id', 2)
                            ->where('cantidad', 200)
                            ->where('compra', '80.00')
                            ->where('venta', '125.00')
                            ->etc();
                    })->has('data.2', function ($notAddedJson) {
                        $notAddedJson
                            ->etc()
                            ->has('producto.codigo_barras')
                            ->has('producto.nombre')
                            ->has('producto.descripcion')
                            ->where('producto_id', 4)
                            ->where('cantidad', 8)
                            ->where('compra', '80.00')
                            ->where('venta', '125.00')
                            ->etc();
                    })->etc();
            });
    }


    public function test_destroy_when_called_remove_and_log_saved()
    {

        $this->actingAs($this->_user);

        $codigoBarras = '001';
        $producto = $this->showProductByCode($codigoBarras);

        $inventarioArray = Inventario::with('producto')->where('producto_id', $producto->id)
            ->first()->toArray();

        $this->delete($this->_endpoint . '/' . $codigoBarras)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseMissing('inventarios', [
            'producto_id' => $producto->id
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Registro de inventario eliminado exitosamente.',
            'antes' => json_encode($inventarioArray)
        ]);
    }

    /**
     * @testWith ["venta"]
     *           ["compra"]
     *           ["cantidad"]
     */
    public function test_patch_when_called_update_and_log_saved(string $param)
    {

        $this->actingAs($this->_user);
        $codigoBarras = '001';
        $producto = $this->showProductByCode($codigoBarras);

        $inventarioArray = Inventario::with('producto')->where('producto_id', $producto->id)
            ->first()->toArray();
        $input = [
            $param => 10
        ];
        $this->patch($this->_endpoint . '/' . $codigoBarras, $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('inventarios', [
            'producto_id' => $producto->id,
            $param => $input[$param]
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Registro de inventario actualizado exitosamente.',
            'antes' => json_encode($inventarioArray),
            'despues' => json_encode($input)
        ]);
    }

    public function test_patch_when_error_log_saved()
    {

        $this->actingAs($this->_user);

        $codigoBarras = '001';

        $input = [
            'compra' => 'Number was expected'
        ];
        $this->patch($this->_endpoint . '/' . $codigoBarras, $input)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Error al actualizar item en el inventario.',
            'antes' => json_encode($input)
        ]);
    }




    public function test_finish_when_called_return_correct_result_and_log_saved()
    {

        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/finish')
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('productos', [
            'codigo_barras' => '001',
            'stock' => 100,
            'compra' => 100,
            'venta' => 160
        ]);

        $this->assertDatabaseHas('productos', [
            'codigo_barras' => '002',
            'stock' => 200,
            'compra' => 80,
            'venta' => 125
        ]);

        $this->assertDatabaseHas('productos', [
            'codigo_barras' => '003',
            'stock' => 0,
            'compra' => 75,
            'venta' => 100
        ]);

        $this->assertDatabaseHas('productos', [
            'codigo_barras' => '004',
            'stock' => 8,
            'compra' => 80,
            'venta' => 125
        ]);

        $this->assertDatabaseHas('productos', [
            'codigo_barras' => '005',
            'stock' => 1,
            'compra' => 100,
            'venta' => 200
        ]);

        $this->assertDatabaseHas('inventarios', [
            'producto_id' => 1,
            'procesado' => 1
        ]);

        $this->assertDatabaseHas('inventarios', [
            'producto_id' => 4,
            'procesado' => 1
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Se finalizó el re-inventario correctamente.'
        ]);
    }




    private function showProductByCode($codigo)
    {
        return Producto::where('codigo_barras', $codigo)->first();
    }
}
