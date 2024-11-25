<?php

namespace Tests\Feature;

use App\AperturaCaja;
use App\Enums\AperturaCajaEstado;
use App\Repositories\ConfiguracionRepository;
use App\User;
use Database\Seeders\PuntoVentaControllerTestingSeed;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PuntoVentaControllerTest extends TestCase
{
    private $_endpoint = '/caja';
    private $_user;
    private $_inputs = [];


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PuntoVentaControllerTestingSeed::class);
        $this->_user = User::find(2);
        $this->_inputs = [
            'inicial_efe' => fake()->numberBetween(100, 1000),
            'inicial_ele' => fake()->numberBetween(0, 200),
            'observaciones' => fake()->realText(15),
            'inicial_apartados' => fake()->numberBetween(100, 1000),
            'inicial_recargas_servicios' => fake()->numberBetween(100, 1000)
        ];
    }

    public function test_opening_when_called_return_correct_form()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/opening')
            ->assertOk()
            ->assertSee([
                'Dinero inicial en caja',
                'Dinero en Mercado Pago',
                'Dinero inicial en apartados',
                'Dinero inicial en recargas/servicios'
            ]);
    }

    public function test_open_when_called_registered_correctly()
    {
        $this->actingAs($this->_user);

        $this->post($this->_endpoint . '/open', $this->_inputs)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('configuraciones', [
            'clave' => 'ESTADO_CAJA',
            'valor' => 'abierta'
        ]);
        $this->assertDatabaseHas('aperturas_caja', [
            'inicial_efe' => $this->_inputs['inicial_efe'],
            'inicial_ele' => $this->_inputs['inicial_ele'],
            'observaciones' => $this->_inputs['observaciones'],
            'inicial_apartados' => $this->_inputs['inicial_apartados'],
            'inicial_recargas_servicios' => $this->_inputs['inicial_recargas_servicios'],
        ]);
    }

    public function test_open_when_no_valid_return_invalid_and_dont_save_anything()
    {
        (new ConfiguracionRepository())->set('ESTADO_CAJA', 'cerrada');
        $this->actingAs($this->_user);

        unset($this->_inputs['inicial_apartados']);
        unset($this->_inputs['inicial_recargas_servicios']);

        $this->post($this->_endpoint . '/open', $this->_inputs)
            ->assertInvalid()
            ->assertRedirectToRoute('punto-venta.opening')
            ->assertSessionHasErrors(['inicial_apartados', 'inicial_recargas_servicios']);

        $this->assertDatabaseMissing('configuraciones', [
            'clave' => 'ESTADO_CAJA',
            'valor' => 'abierta'
        ]);
        $this->assertDatabaseMissing('aperturas_caja', [
            'observaciones' => $this->_inputs['observaciones']
        ]);
    }

    public function test_open_when_caja_opened_return_ok_but_dont_save_anything()
    {
        $this->actingAs($this->_user);

        $this->createAperturaCaja();

        $this->post($this->_endpoint . '/open', $this->_inputs)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseMissing('aperturas_caja', [
            'inicial_efe' => $this->_inputs['inicial_efe'],
            'inicial_ele' => $this->_inputs['inicial_ele'],
            'observaciones' => $this->_inputs['observaciones'],
            'inicial_apartados' => $this->_inputs['inicial_apartados'],
            'inicial_recargas_servicios' => $this->_inputs['inicial_recargas_servicios']
        ]);
    }

    public function test_open_when_error_return_flash_and_new_log_record_created()
    {
        (new ConfiguracionRepository())->set('ESTADO_CAJA', 'cerrada');
        $this->actingAs($this->_user);

        $this->_inputs['inicial_ele'] = 'Expected a number';

        $this->post($this->_endpoint . '/open', $this->_inputs)
            ->assertOk()
            ->assertSessionHas('flash');

        $this->assertDatabaseMissing('configuraciones', [
            'clave' => 'ESTADO_CAJA',
            'valor' => 'abierta'
        ]);

        $this->assertDatabaseMissing('aperturas_caja', [
            'inicial_efe' => $this->_inputs['inicial_efe'],
            'inicial_ele' => $this->_inputs['inicial_ele'],
            'observaciones' => $this->_inputs['observaciones']
        ]);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Error al aperturar la caja.',
        ]);
    }



    public function test_close_when_called_updated_record_correctly_and_log_saved()
    {
        $this->actingAs($this->_user);

        $apertura = $this->createAperturaCaja();

        $this->followingRedirects()->get($this->_endpoint . '/close')
            ->assertOk()
            ->assertSee(['Abrir caja', 'La caja está cerrada y no podrá realizar ventas']);

        $this->assertDatabaseHas('aperturas_caja', [
            'inicial_efe' => $apertura->inicial_efe,
            'inicial_ele' => $apertura->inicial_ele,
            'inicial_apartados' => $apertura->inicial_apartados,
            'gastos_efe' => 1368,
            'gastos_ele' => 10,
            'apartados_dia' => 117,
            'servicios_recargas_efe' => 178,
            'servicios_recargas_ele' => 0,
            'ventas_efe' => 1550,
            'ventas_ele' => 655.72,
            'subtotal_efe' => 1065,
            'subtotal_ele' => 845.72,
            'subtotal_apartados' => 837,
            'subtotal_recargas_servicios' => 878,
            'total' => 1910.72,
            'utilidades' => 803.72,
            'estado' => AperturaCajaEstado::Concluido->value,
            'observaciones' => $apertura->observaciones,
        ]);

        $apertura = $apertura->fresh();
        $this->assertDatabaseHas('log', [
            'descripcion' => 'Se cerró la caja correctamente.',
            'despues' => json_encode($apertura)
        ]);
    }

    public function test_index_when_called_show_correct_view()
    {
        (new ConfiguracionRepository())->set('ESTADO_CAJA', 'cerrada');
        $this->actingAs($this->_user);

        $this->get('/caja')
            ->assertOk()
            ->assertSee(['Abrir caja', 'La caja está cerrada y no podrá realizar ventas']);
    }

    public function test_index_when_caja_opened_show_correct_button()
    {
        (new ConfiguracionRepository())->set('ESTADO_CAJA', 'abierta');
        $this->actingAs($this->_user);

        $this->get('/caja')
            ->assertOk()
            ->assertSee(['HACER CORTE']);
    }

    public function test_reprintLast_when_called_return_correct_json()
    {
        $this->actingAs($this->_user);

        $this->post($this->_endpoint . '/open', [
            'inicial_efe' => 1000,
            'inicial_ele' => 200, 'inicial_apartados' => 1500,
            'inicial_recargas_servicios' => 700
        ]);
        $this->get($this->_endpoint . '/close');

        $this->get($this->_endpoint . '/reprint-last')
            ->assertOk()
            ->assertJson(function (AssertableJson $json) {
                $json->etc()
                    ->where('ventas_efe', '1550.00')
                    ->where('ventas_ele', '655.72')
                    ->where('servicios_recargas_efe', '178.00')
                    ->where('subtotal_recargas_servicios', '878.00')
                    ->where('subtotal_efe', '1065.00')
                    ->where('subtotal_ele', '845.72')
                    ->where('subtotal_apartados', '837.00')
                    ->where('total', '1910.72')
                    ->where('utilidades', '803.72')
                    ->etc();
            });
    }

    public function test_cobro_when_called_return_correct_view()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/cobro')
            ->assertOk()
            ->assertSee(['Cobro', 'Total', 'Método', 'COBRAR']);
    }

    public function test_descuento_when_called_return_correct_view()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/descuento/1')
            ->assertOk()
            ->assertSee(['Tipo', 'Monto', 'Total', 'Motivo', 'REALIZAR DESCUENTO']);
    }

    public function test_descuento_when_not_found_return_404()
    {
        $this->actingAs($this->_user);

        $this->get($this->_endpoint . '/descuento/999')
            ->assertNotFound();
    }



    private function createAperturaCaja($estado = 'Pendiente')
    {
        $apertura = new AperturaCaja();
        $apertura->inicial_efe = 1000;
        $apertura->inicial_ele = 200;
        $apertura->inicial_apartados = 1500;
        $apertura->inicial_recargas_servicios = 700;
        $apertura->observaciones = fake()->realText(15);
        $apertura->estado = $estado;
        $apertura->save();

        return $apertura;
    }
}
