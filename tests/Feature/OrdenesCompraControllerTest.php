<?php

namespace Tests\Feature;

use App\Enums\OrdenCompraEstado;
use App\OrdenCompra;
use App\Producto;
use App\Proveedor;
use App\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\OrdenesCompra\TestingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrdenesCompraControllerTest extends TestCase
{
    private $_user;
    private $_endpoint = '/api/ordenes-compra';
    private $_ordenesCompra;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
        $this->_ordenesCompra = OrdenCompra::orderByRaw("(CASE 
        when estado = '" . OrdenCompraEstado::Pendiente->value . "' THEN 0
        when estado = '" . OrdenCompraEstado::Pedido->value . "' THEN 1
        when estado = '" . OrdenCompraEstado::Recibido->value . "' THEN 2
        ELSE 3
        END)")->orderBy('id', 'desc')->take(50)->get()->toArray();
    }

    public function test_index_when_called_return_correct_json()
    {
        $this->actingAs($this->_user);
        $this->get($this->_endpoint)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data', $this->_ordenesCompra);
    }

    public function test_index_when_not_authorized_return_correct_status()
    {
        $user = User::find(2);
        $this->actingAs($user);
        $this->get($this->_endpoint)
            ->assertForbidden();
    }

    public function test_index_when_not_authenticated_return_correct_status()
    {
        $this->get($this->_endpoint)
            ->assertRedirectToRoute('login');
    }



    public function test_show_when_called_show_correct_orden_compra()
    {
        $ordenCompra = OrdenCompra::find(1);

        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/' . $ordenCompra->id)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data', $ordenCompra->toArray());
    }

    public function test_show_when_not_authorized_show_correct_status()
    {
        $ordenCompra = OrdenCompra::find(1);

        $user = User::find(2);
        $this->actingAs($user);

        $this->get($this->_endpoint . '/' . $ordenCompra->id)
            ->assertForbidden();
    }

    public function test_show_when_not_authenticated_redirect_login()
    {
        $ordenCompra = OrdenCompra::find(1);

        $this->get($this->_endpoint . '/' . $ordenCompra->id)
            ->assertRedirectToRoute('login');
    }

    public function test_show_when_not_exist_return_correct_status()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/1000')
            ->assertNotFound();
    }



    public function test_suggested_return_correct_json()
    {
        $this->actingAs($this->_user);
        $this->get($this->_endpoint . '/faltantes/suggested/1')
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJson(function (AssertableJson $json) {
                $json->etc()
                    ->has('data.0', function (AssertableJson $json) {
                        $json->etc()
                            ->where('codigo_barras', '006')
                            ->where('sugerido', '2')
                            ->etc();
                    })
                    ->missing('data.1', function (AssertableJson $json0) {
                        $json0->etc()
                            ->where('codigo_barras', '001')
                            ->where('sugerido', '4')
                            ->etc();
                    })
                    ->missing('data.2', function (AssertableJson $json0) {
                        $json0->etc()
                            ->where('codigo_barras', '002')
                            ->where('sugerido', '6')
                            ->etc();
                    })
                    ->missing('data.3', function (AssertableJson $json0) {
                        $json0->etc()
                            ->where('codigo_barras', '003')
                            ->where('sugerido', '7')
                            ->etc();
                    })
                    ->etc();
            });
    }

    public function test_suggested_when_no_auth_return_error()
    {
        $user = User::find(2);

        $this->actingAs($user);
        $this->get($this->_endpoint . '/faltantes/suggested')
            ->assertForbidden();
    }


    public function test_notAvailable_return_correct_json()
    {
        $this->actingAs($this->_user);
        $this->get($this->_endpoint . '/faltantes/not-available')
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJson(function (AssertableJson $json) {
                $json->etc()
                    ->has('data.0', function (AssertableJson $json0) {
                        $json0->etc()
                            ->where('codigo_barras', '005')
                            ->etc();
                    })
                    ->etc();
            });
    }

    public function test_notAvailable_when_no_auth_return_error()
    {
        $user = User::find(2); // No auth

        $this->actingAs($user);
        $this->get($this->_endpoint . '/faltantes/not-available')
            ->assertForbidden();
    }



    public function test_addItem_when_called_create_oc_and_add_item_correctly_saving_log()
    {
        $this->actingAs($this->_user);

        $input = [
            'proveedor_id' => Proveedor::where('nombre', 'Quepharma')->first()->id,
            'producto_id' => 1,
            'cantidad' => 1
        ];

        $this->post($this->_endpoint . '/items/add', $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al guardar la orden de compra.',
            'despues' => json_encode($input)
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al agregar item a la orden de compra.',
            'despues' => json_encode($input)
        ]);

        $this->assertDatabaseHas('ordenes_compra', [
            'proveedor_id' => $input['proveedor_id'],
            'creador_id' => $this->_user->id,
            'estado' => OrdenCompraEstado::Pendiente->value
        ]);

        $this->assertDatabaseHas('ordenes_compra_productos', [
            'producto_id' => $input['producto_id'],
            'cantidad' => $input['cantidad']
        ]);
    }

    public function test_addItem_when_oc_exists_add_item_only_saving_log_correctly()
    {
        $this->actingAs($this->_user);

        $input = [
            'proveedor_id' => Proveedor::where('nombre', 'Levic')->first()->id,
            'producto_id' => 2,
            'cantidad' => 2
        ];

        $this->post($this->_endpoint . '/items/add', $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseMissing('log', [
            'descripcion' => 'Ok al guardar la orden de compra.',
            'despues' => json_encode($input)
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al agregar item a la orden de compra.',
            'despues' => json_encode($input)
        ]);

        $this->assertDatabaseHas('ordenes_compra_productos', [
            'producto_id' => $input['producto_id'],
            'cantidad' => $input['cantidad']
        ]);
    }

    public function test_addItem_when_unathorized_return_correct_error()
    {
        $user = User::find(2); // Not authorized
        $this->actingAs($user);

        $input = [
            'proveedor_id' => Proveedor::where('nombre', 'Levic')->first()->id,
            'identificador' => 'PROV002',
            'producto_id' => 2,
            'cantidad' => 2,
            'compra' => 87.6
        ];

        $this->post($this->_endpoint . '/items/add', $input)
            ->assertForbidden();
    }

    public function test_addItem_when_unathenticated_redirect_login()
    {
        $input = [
            'proveedor_id' => Proveedor::where('nombre', 'Levic')->first()->id,
            'identificador' => 'PROV002',
            'producto_id' => 2,
            'cantidad' => 2,
            'compra' => 87.6
        ];

        $this->post($this->_endpoint . '/items/add', $input)
            ->assertRedirectToRoute('login');
    }

    public function test_addItem_when_invalid_return_correct_messages()
    {
        $this->actingAs($this->_user);

        $input = [
            'proveedor_id' => Proveedor::where('nombre', 'Levic')->first()->id,
            'producto_id' => 2
        ];

        $this->post($this->_endpoint . '/items/add', $input)
            ->assertInvalid()
            ->assertSessionHasErrors(['cantidad']);
    }



    public function test_patchItem_when_called_edit_item_and_log_saved()
    {
        $this->actingAs($this->_user);

        $input = [
            'cantidad' => 2
        ];

        $ordenCompraId = 1;
        $productoId = 1;

        $this->patch($this->_endpoint . "/$ordenCompraId/items/$productoId", $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al actualizar item de la orden de compra.',
            'despues' => json_encode($input)
        ]);

        $this->assertDatabaseHas('ordenes_compra_productos', [
            'producto_id' => $productoId,
            'cantidad' => $input['cantidad']
        ]);
    }

    public function test_patchItem_when_unathorized_return_correct_error()
    {
        $user = User::find(2); // Not authorized
        $this->actingAs($user);

        $input = [
            'cantidad' => 2
        ];

        $this->patch($this->_endpoint . '/1/items/1', $input)
            ->assertForbidden();
    }

    public function test_patchItem_when_unathenticated_redirect_login()
    {
        $input = [
            'identificador' => 'PROV002',
            'cantidad' => 2,
            'compra' => 87.6
        ];

        $this->patch($this->_endpoint . '/1/items/1', $input)
            ->assertRedirectToRoute('login');
    }

    public function test_patchItem_when_invalid_return_correct_messages()
    {
        $this->actingAs($this->_user);

        $input = [];

        $this->patch($this->_endpoint . '/1/items/1', $input)
            ->assertInvalid()
            ->assertSessionHasErrors(['cantidad']);
    }



    public function test_deleteItem_when_called_remove_item_and_log_saved()
    {
        $this->actingAs($this->_user);

        $this->delete($this->_endpoint . '/1/items/1')
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al eliminar item de la orden de compra.',
            'link_id' => 1
        ]);

        $this->assertDatabaseMissing('ordenes_compra_productos', [
            'producto_id' => 1,
            'orden_compra_id' => 1
        ]);
    }

    public function test_deleteItem_when_unathorized_return_correct_error()
    {
        $user = User::find(2); // Not authorized
        $this->actingAs($user);

        $this->delete($this->_endpoint . '/1/items/1')
            ->assertForbidden();
    }

    public function test_deleteItem_when_unathenticated_redirect_login()
    {
        $this->delete($this->_endpoint . '/1/items/1')
            ->assertRedirectToRoute('login');
    }




    public function test_patch_when_called_updated_and_log_saved()
    {
        $this->actingAs($this->_user);

        $input = [
            'estado' => 'Pedido'
        ];

        $this->patch($this->_endpoint . '/1', $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('ordenes_compra', [
            'id' => 1,
            'estado' => $input['estado']
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Se actualizó la orden de compra correctamente.',
            'link_id' => 1,
            'despues' => json_encode($input)
        ]);
    }

    public function test_patch_when_not_authorized_return_correct_result()
    {
        $user = User::find(2); // No authorized
        $this->actingAs($user);

        $input = [
            'estado' => 'Pedido'
        ];

        $this->patch($this->_endpoint . '/1', $input)
            ->assertForbidden();
    }

    public function test_patch_when_not_auth_redirect_login()
    {
        $input = [
            'estado' => 'Pedido'
        ];

        $this->patch($this->_endpoint . '/1', $input)
            ->assertRedirectToRoute('login');
    }

    public function test_patch_when_error_log_recorded()
    {
        $this->actingAs($this->_user);


        $input = [
            'estado' => 'Unkown' // Out of enum
        ];

        $this->patch($this->_endpoint . '/1', $input)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'link_id' => 1,
            'descripcion' => 'Error al actualizar la orden de compra.'
        ]);
    }
}
