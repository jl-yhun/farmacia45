<?php

namespace Tests\Feature;

use App\Producto;
use App\Repositories\ProductosRepository;
use App\User;
use Database\Seeders\Productos\TestingSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProductosControllerTest extends TestCase
{
    private $_endpoint = '/api/productos';
    private $_user;
    private $_inputProductosProveedores;
    private $_productoId = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
        $this->_inputProductosProveedores = [
            'proveedor_id' => 3,
            'codigo' => 'CODE',
            'disponible' => true,
            'precio' => '12.00'
        ];
    }

    public function test_index_not_authorized_correct_status()
    {
        $user = User::find(3); // Not auth
        $this->actingAs($user);

        $this->get($this->_endpoint)
            ->assertForbidden();
    }

    public function test_index_not_authenticated_redirect_login()
    {
        $this->get($this->_endpoint)
            ->assertRedirectToRoute('login');
    }



    public function test_store_when_called_save_correctly_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'nombre' => fake()->name(),
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => fake()->numberBetween(50, 100),
            'venta' => fake()->numberBetween(101, 500),
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => fake()->numberBetween(0, 2),
            'max_stock' => fake()->numberBetween(3, 5)
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->etc()
                    ->has('data')
                    ->has('data.nombre')
                    ->has('data.codigo_barras')
                    ->has('data.compra')
                    ->has('data.min_stock')
                    ->has('data.max_stock')
                    ->etc();
            });

        $this->assertDatabaseHas('productos', $newProduct);
        $this->assertDatabaseHas('log', [
            'modulo' => ProductosRepository::class,
            'despues' => json_encode($newProduct)
        ]);
    }

    public function test_store_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'nombre' => fake()->name(),
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => fake()->numberBetween(50, 100),
            'venta' => fake()->numberBetween(101, 500),
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => 1,
            'max_stock' => 2
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertForbidden();
    }

    public function test_store_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'nombre' => fake()->name(),
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => fake()->numberBetween(50, 100),
            'venta' => fake()->numberBetween(101, 500),
            'stock' => 'Expected a number',
            'min_stock' => 1,
            'max_stock' => 2
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'modulo' => ProductosRepository::class,
            'antes' => json_encode($newProduct)
        ]);
        $this->assertDatabaseMissing('log', [
            'modulo' => ProductosRepository::class,
            'despues' => json_encode($newProduct)
        ]);
    }

    public function test_store_when_no_valid_no_saved_return_error_message()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => fake()->numerify('###.#'),
            'venta' => fake()->numerify('###.#'),
            'stock' => fake()->numberBetween(2, 100)
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertInvalid()
            ->assertSessionHasErrors(['nombre', 'min_stock', 'max_stock'])
            ->assertSessionHasInput(['compra', 'venta', 'descripcion']);
    }

    public function test_store_when_venta_less_than_compra()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'nombre' => fake()->name(),
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 50,
            'venta' => 10,
            'stock' => fake()->numberBetween(2, 100)
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertInvalid()
            ->assertSessionHasErrors('venta')
            ->assertSessionHasInput(['compra', 'venta', 'descripcion']);
    }

    public function test_store_when_no_codigo_barras_save_correctly()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => 1,
            'max_stock' => 2
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertOk();

        $this->assertDatabaseHas('productos', $newProduct);
        $this->assertDatabaseHas('log', [
            'modulo' => ProductosRepository::class,
            'despues' => json_encode($newProduct)
        ]);
    }

    public function test_store_when_duplicated_return_validation_error()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => '001', // Duplicated
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100)
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertInvalid()
            ->assertSessionHasErrors('codigo_barras');
    }

    public function test_store_when_granel_create_granel_product_record()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => fake()->numberBetween(0, 2),
            'max_stock' => fake()->numberBetween(3, 5),
            'isGranel' => true,
            'unidades_paquete' => 2
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->etc()
                    ->has('data')
                    ->has('data.nombre')
                    ->has('data.codigo_barras')
                    ->has('data.compra')
                    ->has('data.min_stock')
                    ->has('data.max_stock')
                    ->etc();
            });

        $this->assertDatabaseHas('productos_granel', [
            'unidades_paquete' => $newProduct['unidades_paquete']
        ]);

        $this->assertDatabaseHas('log', [
            'modulo' => ProductosRepository::class,
            'despues' => json_encode($newProduct)
        ]);


        unset($newProduct['isGranel']);
        unset($newProduct['unidades_paquete']);

        $this->assertDatabaseHas('productos', $newProduct);
    }

    public function test_store_when_granel_validation_error()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => fake()->numberBetween(0, 2),
            'max_stock' => fake()->numberBetween(3, 5),
            'isGranel' => 1,
            'unidades_paquete' => 0
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertInvalid()
            ->assertSessionHasErrors(['unidades_paquete']);
    }

    public function test_store_when_granel_validation_error_2()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => fake()->numberBetween(0, 2),
            'max_stock' => fake()->numberBetween(3, 5),
            'isGranel' => 1
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertInvalid()
            ->assertSessionHasErrors(['unidades_paquete']);
    }



    public function test_patch_when_called_save_correctly_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $updatedProduct = [
            'stock' => -333444,
            'codigo_barras' => '12345',
            'categoria_id' => 1,
            'nombre' => 'Producto 1',
            'caducidad' => '2025-01-01',
            'compra' => 10,
            'venta' => 15,
            'stock' => 1,
            'min_stock' => 1,
            'max_stock' => 2,
            'isGranel' => 0,
            'unidades_paquete' => 0
        ];

        $this->patch($this->_endpoint . '/3', $updatedProduct)
            ->assertStatus(200);

        $this->assertDatabaseHas('log', [
            'modulo' => ProductosRepository::class,
            'despues' => json_encode($updatedProduct)
        ]);
        unset($updatedProduct['isGranel']);
        unset($updatedProduct['unidades_paquete']);
        $this->assertDatabaseHas('productos', $updatedProduct);
    }

    public function test_patch_when_no_valid_no_saved_return_error_message()
    {
        $this->actingAs($this->_user);

        $productoToUpdate = [
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => fake()->numerify('###.#'),
            'venta' => fake()->numerify('###.#'),
            'stock' => fake()->numberBetween(2, 100)
        ];

        $this->patch($this->_endpoint . '/1', $productoToUpdate)
            ->assertInvalid()
            ->assertSessionHasErrors(['nombre', 'min_stock', 'max_stock'])
            ->assertSessionHasInput(['compra', 'venta', 'descripcion']);
    }

    public function test_patch_when_patching_stock_without_permission_return_false_and_log_recorded()
    {
        $anotherUserWithoutAdminRole = User::find(2);
        $this->actingAs($anotherUserWithoutAdminRole);

        $updatedProduct = [
            'stock' => -333444,
            'codigo_barras' => '12345',
            'categoria_id' => 1,
            'nombre' => 'Producto 1',
            'caducidad' => '2025-01-01',
            'compra' => 10,
            'venta' => 15,
            'stock' => 1,
            'min_stock' => 1,
            'max_stock' => 2
        ];

        $this->patch($this->_endpoint . '/3', $updatedProduct)
            ->assertInvalid();

        $this->assertDatabaseMissing('productos', $updatedProduct);
        $this->assertDatabaseHas('log', [
            'descripcion' => 'Intento de actualización al stock',
            'link_id' => 3
        ]);
    }

    public function test_patch_when_venta_less_than_compra()
    {
        $this->actingAs($this->_user);

        $productoToUpdate = [
            'nombre' => fake()->name(),
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 50,
            'venta' => 10,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => 1,
            'max_stock' => 2
        ];

        $this->patch($this->_endpoint . '/1', $productoToUpdate)
            ->assertInvalid()
            ->assertSessionHasErrors('venta')
            ->assertSessionHasInput(['compra', 'venta', 'descripcion']);
    }

    public function test_patch_when_not_authorized_return_403_view()
    {
        $user = User::find(3); // Not auth
        $this->actingAs($user);

        $productoToUpdate = [
            'nombre' => fake()->name(),
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 15,
            'stock' => fake()->numberBetween(300, 500),
            'min_stock' => 1,
            'max_stock' => 2
        ];

        $this->patch($this->_endpoint . '/3', $productoToUpdate)
            ->assertForbidden();
    }

    public function test_patch_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $productoToUpdate = [
            'nombre' => fake()->name(),
            'codigo_barras' => fake()->uuid(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 50,
            'venta' => 60,
            'stock' => 'Expected to be a number',
            'min_stock' => 1,
            'max_stock' => 2
        ];

        $this->patch($this->_endpoint . '/3', $productoToUpdate)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'modulo' => ProductosRepository::class,
            'despues' => json_encode($productoToUpdate)
        ]);
        $this->assertDatabaseMissing('log', [
            'modulo' => ProductosRepository::class,
            'antes' => json_encode($productoToUpdate)
        ]);
    }

    public function test_patch_when_duplicated_return_validation_error()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => '001', // Duplicated
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100)
        ];

        $this->patch($this->_endpoint . '/2', $newProduct)
            ->assertInvalid()
            ->assertSessionHasErrors('codigo_barras');
    }

    public function test_patch_when_granel_create_granel_product_record()
    {
        $this->actingAs($this->_user);

        $updatedProduct = [
            'codigo_barras' => fake()->uuid(),
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => 1,
            'max_stock' => 2,
            'isGranel' => true,
            'unidades_paquete' => 2
        ];

        $this->patch($this->_endpoint . '/3', $updatedProduct)
            ->assertOk();

        $this->assertDatabaseHas('log', [
            'modulo' => ProductosRepository::class,
            'despues' => json_encode($updatedProduct)
        ]);

        $this->assertDatabaseHas('productos_granel', [
            'unidades_paquete' => 2,
            'producto_id' => 3
        ]);

        unset($updatedProduct['isGranel']);
        unset($updatedProduct['unidades_paquete']);
        $this->assertDatabaseHas('productos', $updatedProduct);
    }

    public function test_patch_when_granel_validation_error()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => fake()->numberBetween(0, 2),
            'max_stock' => fake()->numberBetween(3, 5),
            'isGranel' => 1,
            'unidades_paquete' => 0
        ];

        $this->patch($this->_endpoint . '/3', $newProduct)
            ->assertInvalid()
            ->assertSessionHasErrors(['unidades_paquete']);
    }

    public function test_patch_when_granel_validation_error_2()
    {
        $this->actingAs($this->_user);

        $newProduct = [
            'codigo_barras' => fake()->uuid(),
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => fake()->numberBetween(0, 2),
            'max_stock' => fake()->numberBetween(3, 5),
            'isGranel' => 1
        ];

        $this->post($this->_endpoint, $newProduct)
            ->assertInvalid()
            ->assertSessionHasErrors(['unidades_paquete']);
    }

    public function test_patch_when_removed_granel_delete_record()
    {
        $this->actingAs($this->_user);

        $updatedProduct = [
            'codigo_barras' => fake()->uuid(),
            'nombre' => fake()->name(),
            'categoria_id' => 1,
            'descripcion' => fake()->realText(50),
            'caducidad' => fake()->date(),
            'compra' => 10,
            'venta' => 50,
            'stock' => fake()->numberBetween(2, 100),
            'min_stock' => fake()->numberBetween(0, 2),
            'max_stock' => fake()->numberBetween(3, 5),
            'isGranel' => 0
        ];

        $this->patch($this->_endpoint . '/3', $updatedProduct)
            ->assertOk();

        $this->assertDatabaseHas('log', [
            'modulo' => ProductosRepository::class,
            'despues' => json_encode($updatedProduct)
        ]);

        $this->assertDatabaseMissing('productos_granel', [
            'producto_id' => 3
        ]);

        unset($updatedProduct['isGranel']);
        unset($updatedProduct['unidades_paquete']);
        $this->assertDatabaseHas('productos', $updatedProduct);
    }



    public function test_destroy_when_called_soft_deleted_correctly()
    {
        $this->actingAs($this->_user);

        $this->delete($this->_endpoint . '/3')
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertSoftDeleted('productos', ['id' => 3]);
    }

    public function test_destroy_when_not_authorized_return_403_view()
    {
        $user = User::find(2); // Not auth
        $this->actingAs($user);

        $this->delete($this->_endpoint . '/3')
            ->assertForbidden();
    }

    /**
     * @dataProvider searchDataProvider
     */
    public function test_buscar_when_called_return_correct_values($search, $expected)
    {
        $this->actingAs($this->_user);

        $productoExpected = Producto::find($expected);

        $this->post('/productos/buscar', ['busqueda' => $search])
            ->assertOk()
            ->assertSee([$productoExpected->nombre, $productoExpected->stock]);
    }



    public function test_proveedores_when_called_return_correct_json()
    {
        $productoProveedores = Producto::find(1)->proveedores->toArray();

        $this->actingAs($this->_user);

        $this->get('/api/productos/1/proveedores')
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJson(function (AssertableJson $json) use ($productoProveedores) {
                $json->etc()
                    ->has('data.0', function (AssertableJson $json) use ($productoProveedores) {
                        $json->etc()
                            ->where('nombre', $productoProveedores[0]['nombre'])
                            ->where('pivot.codigo', $productoProveedores[0]['pivot']['codigo'])
                            ->etc();
                    })
                    ->etc();
            });
    }



    public function test_proveedoresStore_when_called_log_and_record_saved()
    {
        $this->actingAs($this->_user);

        $this->post('/api/productos/' . $this->_productoId . '/proveedores', $this->_inputProductosProveedores)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data.pivot.codigo', strtoupper(trim($this->_inputProductosProveedores['codigo'])))
            ->assertJsonPath('data.pivot.proveedor_id', $this->_inputProductosProveedores['proveedor_id'])
            ->assertJsonPath('data.pivot.precio', $this->_inputProductosProveedores['precio']);

        $this->assertDatabaseHas('productos_proveedores', [
            'producto_id' => $this->_productoId,
            'proveedor_id' => $this->_inputProductosProveedores['proveedor_id'],
            'codigo' => strtoupper(trim($this->_inputProductosProveedores['codigo'])),
            'precio' => $this->_inputProductosProveedores['precio']
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al agregar registro',
            'link_id' => $this->_productoId,
            'modulo' => ProductosRepository::class . '::linkProveedor'
        ]);
    }

    public function test_proveedoresStore_when_default_already_set_return_correct_result()
    {
        $this->actingAs($this->_user);

        $this->_inputProductosProveedores['default'] = true;


        $this->post('/api/productos/' . $this->_productoId . '/proveedores', $this->_inputProductosProveedores)
            ->assertInvalid()
            ->assertSessionHasErrors('default');
    }

    public function test_proveedoresStore_when_not_authorized_return_correct_result()
    {
        $user = User::find(2); // Not authorized

        $this->actingAs($user);


        $this->post('/api/productos/' . $this->_productoId . '/proveedores', $this->_inputProductosProveedores)
            ->assertForbidden();
    }

    public function test_proveedoresStore_when_no_valid_return_correct_result()
    {
        $this->actingAs($this->_user);


        unset($this->_inputProductosProveedores['codigo']);
        unset($this->_inputProductosProveedores['precio']);

        $this->post('/api/productos/' . $this->_productoId . '/proveedores', $this->_inputProductosProveedores)
            ->assertInvalid()
            ->assertSessionHasErrors(['codigo', 'precio'])
            ->assertSessionHasInput(['proveedor_id', 'disponible']);
    }

    public function test_proveedoresStore_when_error_return_correct_result_log_saved()
    {
        $this->actingAs($this->_user);


        $this->_inputProductosProveedores['codigo'] = fake()->text(10000); // Too long

        $this->post('/api/productos/' . $this->_productoId . '/proveedores', $this->_inputProductosProveedores)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'antes' => json_encode($this->_inputProductosProveedores),
            'descripcion' => 'Error al agregar registro',
            'link_id' => $this->_productoId,
            'modulo' => ProductosRepository::class . '::linkProveedor'
        ]);

        $this->assertDatabaseMissing('productos_proveedores', [
            'proveedor_id' => $this->_inputProductosProveedores['proveedor_id'],
            'codigo' => $this->_inputProductosProveedores['codigo'],
            'disponible' => $this->_inputProductosProveedores['disponible'],
        ]);
    }

    public function test_proveedoresStore_when_spaces_in_codigo_log_and_record_saved()
    {
        $this->actingAs($this->_user);

        $this->_inputProductosProveedores['codigo'] = '   codE   ';

        $this->post('/api/productos/' . $this->_productoId . '/proveedores', $this->_inputProductosProveedores)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data.pivot.codigo', strtoupper(trim($this->_inputProductosProveedores['codigo'])))
            ->assertJsonPath('data.pivot.proveedor_id', $this->_inputProductosProveedores['proveedor_id']);

        $this->assertDatabaseHas('productos_proveedores', [
            'producto_id' => $this->_productoId,
            'proveedor_id' => $this->_inputProductosProveedores['proveedor_id'],
            'codigo' => strtoupper(trim($this->_inputProductosProveedores['codigo']))
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al agregar registro',
            'link_id' => $this->_productoId,
            'modulo' => ProductosRepository::class . '::linkProveedor'
        ]);
    }

    public function test_proveedoresStore_when_duplicated_return_correct_result()
    {
        $this->actingAs($this->_user);

        $this->_inputProductosProveedores['proveedor_id'] = 1;

        $this->post('/api/productos/' . $this->_productoId . '/proveedores', $this->_inputProductosProveedores)
            ->assertInvalid()
            ->assertSessionHasErrors(['proveedor_id']);
    }



    public function test_proveedoresUpdate_when_called_log_and_record_saved()
    {
        $this->actingAs($this->_user);

        $this->_inputProductosProveedores['codigo'] = 'Quepharma Code';
        $this->_inputProductosProveedores['proveedor_id'] = 2;
        $this->_inputProductosProveedores['default'] = true;
        $this->_inputProductosProveedores['precio'] = '12.34';

        $this->put('/api/productos/' . $this->_productoId . '/proveedores/' . $this->_inputProductosProveedores['proveedor_id'], $this->_inputProductosProveedores)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data.pivot.codigo', strtoupper(trim($this->_inputProductosProveedores['codigo'])))
            ->assertJsonPath('data.pivot.proveedor_id', $this->_inputProductosProveedores['proveedor_id'])
            ->assertJsonPath('data.pivot.precio', $this->_inputProductosProveedores['precio']);

        $this->assertDatabaseHas('productos_proveedores', [
            'producto_id' => $this->_productoId,
            'proveedor_id' => $this->_inputProductosProveedores['proveedor_id'],
            'codigo' => strtoupper(trim($this->_inputProductosProveedores['codigo'])),
            'precio' => $this->_inputProductosProveedores['precio']
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al editar registro',
            'link_id' => $this->_productoId,
            'modulo' => ProductosRepository::class . '::updateLinkedProveedor'
        ]);
    }

    public function test_proveedoresUpdate_when_default_already_set_return_correct_result()
    {
        $this->actingAs($this->_user);


        $this->_inputProductosProveedores['proveedor_id'] = 1;
        $this->_inputProductosProveedores['default'] = true;


        $this->put('/api/productos/' . $this->_productoId . '/proveedores/' . $this->_inputProductosProveedores['proveedor_id'], $this->_inputProductosProveedores)
            ->assertInvalid()
            ->assertSessionHasErrors('default');
    }

    public function test_proveedoresUpdate_when_not_authorized_return_correct_result()
    {
        $user = User::find(2); // Not authorized

        $this->actingAs($user);


        $this->put('/api/productos/' . $this->_productoId . '/proveedores/' . $this->_inputProductosProveedores['proveedor_id'], $this->_inputProductosProveedores)
            ->assertForbidden();
    }

    public function test_proveedoresUpdate_when_no_valid_return_correct_result()
    {
        $this->actingAs($this->_user);


        unset($this->_inputProductosProveedores['codigo']);
        unset($this->_inputProductosProveedores['precio']);

        $this->put('/api/productos/' . $this->_productoId . '/proveedores/' . $this->_inputProductosProveedores['proveedor_id'], $this->_inputProductosProveedores)
            ->assertInvalid()
            ->assertSessionHasErrors(['codigo', 'precio'])
            ->assertSessionHasInput(['proveedor_id', 'disponible']);
    }

    public function test_proveedoresUpdate_when_error_return_correct_result_log_saved()
    {
        $this->actingAs($this->_user);


        $this->_inputProductosProveedores['proveedor_id'] = 2;
        $this->_inputProductosProveedores['codigo'] = fake()->text(10000); // Too long

        $this->put('/api/productos/' . $this->_productoId . '/proveedores/' . $this->_inputProductosProveedores['proveedor_id'], $this->_inputProductosProveedores)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'antes' => json_encode($this->_inputProductosProveedores),
            'descripcion' => 'Error al editar registro',
            'link_id' => $this->_productoId,
            'modulo' => ProductosRepository::class . '::updateLinkedProveedor'
        ]);

        $this->assertDatabaseMissing('productos_proveedores', [
            'proveedor_id' => $this->_inputProductosProveedores['proveedor_id'],
            'codigo' => $this->_inputProductosProveedores['codigo'],
            'disponible' => $this->_inputProductosProveedores['disponible'],
        ]);
    }

    public function test_proveedoresUpdate_when_spaces_in_codigo_log_and_record_saved()
    {
        $this->actingAs($this->_user);

        $this->_inputProductosProveedores['proveedor_id'] = 2;
        $this->_inputProductosProveedores['codigo'] = '   codE   ';

        $this->put('/api/productos/' . $this->_productoId . '/proveedores/' . $this->_inputProductosProveedores['proveedor_id'], $this->_inputProductosProveedores)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data.pivot.codigo', strtoupper(trim($this->_inputProductosProveedores['codigo'])))
            ->assertJsonPath('data.pivot.proveedor_id', $this->_inputProductosProveedores['proveedor_id']);

        $this->assertDatabaseHas('productos_proveedores', [
            'producto_id' => $this->_productoId,
            'proveedor_id' => $this->_inputProductosProveedores['proveedor_id'],
            'codigo' => strtoupper(trim($this->_inputProductosProveedores['codigo']))
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al editar registro',
            'link_id' => $this->_productoId,
            'modulo' => ProductosRepository::class . '::updateLinkedProveedor'
        ]);
    }

    public function test_proveedoresDelete_when_called_log_and_record_removed()
    {
        $this->actingAs($this->_user);

        $proveedorId = 2;

        $this->delete('/api/productos/' . $this->_productoId . '/proveedores/' . $proveedorId)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseMissing('productos_proveedores', [
            'producto_id' => $this->_productoId,
            'proveedor_id' => $proveedorId
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al eliminar registro',
            'link_id' => $this->_productoId,
            'modulo' => ProductosRepository::class . '::deleteLinkedProveedor'
        ]);
    }

    private function searchDataProvider()
    {
        return [
            ['02 DESC', 2],
            ['003', 3],
            ['01', 1],
            ['descripcióñ', 4]
        ];
    }
}
