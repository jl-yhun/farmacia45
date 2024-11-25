<?php

namespace Tests\Feature;

use App\Enums\MetodoPago;
use App\Enums\TipoDescuento;
use App\Events\MinStockReached;
use App\User;
use Database\Seeders\VentasControllerTestSeed;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class VentasControllerTest extends TestCase
{
    private $_user;
    private array $_inputVenta;
    private $url = '/ventas';
    private $_initCajaParams = [
        'inicial_efe' => 1000, 'inicial_ele' => 0,
        'inicial_apartados' => 1500,
        'inicial_recargas_servicios' => 700
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(VentasControllerTestSeed::class);
        $this->_user = User::find(1);
        $this->_inputVenta =  [
            'metodo_pago' => MetodoPago::Efectivo->value,
            'se-recibe' => 500,
            'total' => 490,
            'productos' => [
                [
                    'id' => 1,
                    'cantidad' => 1,
                    'venta' => 350
                ],
                [
                    'id' => 2,
                    'cantidad' => 2,
                    'venta' => 140
                ]
            ],
            'descuentos' => [
                [
                    'motivo' => fake()->realText(15),
                    'descuento' => 20,
                    'nuevo' => 140,
                    'usuario_id' => 1,
                    'id' => 2,
                    'tipo' => TipoDescuento::Monto->value
                ]
            ]
        ];
    }

    public function test_showJson_when_called_return_correct_json()
    {
        $this->actingAs($this->_user);

        $this->get($this->url . '/1/json')
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->etc()
                    ->where('metodo_pago', MetodoPago::Efectivo->value)
                    ->where('total', '400.00')
                    ->where('denominacion', '500.00')
                    ->where('cambio', '100.00')
                    ->etc();
            });
    }

    public function test_showJson_when_not_found_return_404()
    {
        $this->actingAs($this->_user);

        $this->get($this->url . '/1000/json')
            ->assertNotFound();
    }

    public function test_create_when_called_new_venta_created_log_recorded()
    {
        $this->actingAs($this->_user);

        $this->post('/caja/open', $this->_initCajaParams);

        $this->post($this->url . '/create', $this->_inputVenta)
            ->assertOk()
            ->assertJsonPath('callback', 'imprimirTicketVenta');

        $this->assertDatabaseHas('productos', [
            'id' => 1,
            'stock' => 99
        ]);

        $this->assertDatabaseHas('productos', [
            'id' => 2,
            'stock' => 198
        ]);

        $this->assertDatabaseHas('ventas', [
            'metodo_pago' => $this->_inputVenta['metodo_pago'],
            'denominacion' => $this->_inputVenta['se-recibe'],
            'total' => $this->_inputVenta['total'],
        ]);

        $this->assertDatabaseHas('ventas_productos', [
            'producto_id' => $this->_inputVenta['productos'][0]['id'],
            'cantidad' => $this->_inputVenta['productos'][0]['cantidad'],
            'venta' => $this->_inputVenta['productos'][0]['venta'],
        ]);

        $this->assertDatabaseHas('descuentos', [
            'producto_id' => $this->_inputVenta['descuentos'][0]['id'],
            'motivo' => $this->_inputVenta['descuentos'][0]['motivo']
        ]);
    }

    public function test_create_when_validation_error_return_validation_issue()
    {
        $this->actingAs($this->_user);
        $this->post('/caja/open', ['inicial' => 1000]);

        $this->_inputVenta['se-recibe'] = 20; // Less than expected

        $this->post($this->url . '/create', $this->_inputVenta)
            ->assertInvalid()
            ->assertSessionHasErrors('se-recibe');

        $this->assertDatabaseMissing('productos', [
            'id' => 1,
            'stock' => 99
        ]);

        $this->assertDatabaseMissing('productos', [
            'id' => 2,
            'stock' => 199
        ]);

        $this->assertDatabaseMissing('ventas', [
            'metodo_pago' => $this->_inputVenta['metodo_pago'],
            'denominacion' => $this->_inputVenta['se-recibe'],
            'total' => $this->_inputVenta['total'],
        ]);

        $this->assertDatabaseMissing('ventas_productos', [
            'producto_id' => $this->_inputVenta['productos'][0]['id'],
            'cantidad' => $this->_inputVenta['productos'][0]['cantidad'],
            'venta' => $this->_inputVenta['productos'][0]['venta'],
        ]);

        $this->assertDatabaseMissing('descuentos', [
            'producto_id' => $this->_inputVenta['descuentos'][0]['id'],
            'motivo' => $this->_inputVenta['descuentos'][0]['motivo']
        ]);
    }

    public function test_create_when_error_record_log_return_message()
    {
        $this->actingAs($this->_user);
        $this->post('/caja/open', ['inicial' => 1000]);

        $this->_inputVenta['metodo_pago'] = 'Another not specified'; // Not expected, out of ENUM

        $this->post($this->url . '/create', $this->_inputVenta)
            ->assertOk()
            ->assertSessionHas('flash');

        $this->assertDatabaseMissing('productos', [
            'id' => 1,
            'stock' => 99
        ]);

        $this->assertDatabaseMissing('productos', [
            'id' => 2,
            'stock' => 199
        ]);

        $this->assertDatabaseMissing('ventas', [
            'metodo_pago' => $this->_inputVenta['metodo_pago'],
            'denominacion' => $this->_inputVenta['se-recibe'],
            'total' => $this->_inputVenta['total'],
            'utilidad' => 75,
            'cambio' => 45
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Error al realizar venta.',
            'antes' => json_encode($this->_inputVenta)
        ]);
    }

    public function test_create_when_no_stock_record_log_return_message()
    {
        $this->actingAs($this->_user);
        $this->post('/caja/open', $this->_initCajaParams);

        $this->_inputVenta['productos'][] = [
            'id' => 5,
            'cantidad' => 1, // No stock available
            'venta' => 200
        ];

        $this->post($this->url . '/create', $this->_inputVenta)
            ->assertOk()
            ->assertSessionHas('flash');

        $this->assertDatabaseMissing('productos', [
            'id' => 1,
            'stock' => 99
        ]);

        $this->assertDatabaseMissing('productos', [
            'id' => 2,
            'stock' => 199
        ]);

        $this->assertDatabaseMissing('productos', [
            'id' => 5,
            'stock' => -1
        ]);

        $this->assertDatabaseMissing('ventas', [
            'metodo_pago' => $this->_inputVenta['metodo_pago'],
            'denominacion' => $this->_inputVenta['se-recibe'],
            'total' => $this->_inputVenta['total'],
            'utilidad' => 75,
            'cambio' => 45
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Stock no puede ser menor a 0.',
            'antes' => json_encode($this->_inputVenta)
        ]);
    }

    public function test_create_when_min_stock_reached_send_event()
    {
        Event::fake();

        $this->actingAs($this->_user);
        $this->post('/caja/open', $this->_initCajaParams);

        $this->_inputVenta['productos'][] = [
            'id' => 3,
            'cantidad' => 300, // This leaves stock 0
            'venta' => 200
        ];

        $this->post($this->url . '/create', $this->_inputVenta)
            ->assertOk();

        Event::assertDispatched(MinStockReached::class);
    }

    public function test_create_when_min_stock_reached_but_similares_available_not_send_event()
    {
        Event::fake();

        $this->actingAs($this->_user);
        $this->post('/caja/open', $this->_initCajaParams);

        $this->_inputVenta['productos'][0] = [
            'id' => 1,
            'cantidad' => 100, // This leaves stock 0
            'venta' => 200
        ];

        $this->post($this->url . '/create', $this->_inputVenta)
            ->assertOk();

        $this->assertDatabaseHas('productos', [
            'id' => 1,
            'stock' => 0
        ]);

        Event::assertNotDispatched(MinStockReached::class);
    }

    public function test_create_when_min_stock_reached_but_pedido_not_send_event()
    {
        Event::fake();

        $this->actingAs($this->_user);
        $this->post('/caja/open', $this->_initCajaParams);

        $this->_inputVenta['productos'][0] = [
            'id' => 4,
            'cantidad' => 1, // This leaves stock 3 which is lower than min_stock 4
            'venta' => 200
        ];

        $this->post($this->url . '/create', $this->_inputVenta)
            ->assertOk();

        $this->assertDatabaseHas('productos', [
            'id' => 4,
            'stock' => 3
        ]);

        Event::assertNotDispatched(MinStockReached::class);
    }
}
