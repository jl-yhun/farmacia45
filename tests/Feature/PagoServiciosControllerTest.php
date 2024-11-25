<?php

namespace Tests\Feature;

use App\Enums\MetodoPago;
use App\Enums\RecargasCompania;
use App\Repositories\PagoServiciosRepository;
use App\User;
use Database\Seeders\PagosServiciosTestsSeeder;
use Tests\TestCase;

class PagoServiciosControllerTest extends TestCase
{
    private $_user;
    private $_endpoint = '/api/pago-servicios';
    private $_initCajaParams = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->_user = User::find(1);
        $this->seed(PagosServiciosTestsSeeder::class);
        $this->_initCajaParams = [
            'inicial_efe' => 1000, 'inicial_ele' => 0,
            'inicial_apartados' => 1500,
            'inicial_recargas_servicios' => 100
        ];
    }

    public function test_recargas_when_called_saved_correctly_and_log_saved()
    {
        $this->actingAs($this->_user);
        $this->post('/caja/open', $this->_initCajaParams);

        $input = [
            'monto' => 10,
            'metodo_pago' => MetodoPago::Efectivo->value,
            'compania' => RecargasCompania::Telcel->value
        ];

        $this->post($this->_endpoint . '/recargas', $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('recargas', $input);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al agregar registro',
            'modulo' => PagoServiciosRepository::class,
            'despues' => json_encode($input)
        ]);
    }

    public function test_recargas_when_invalid_error_return_correctly()
    {
        $this->actingAs($this->_user);
        $input = [
            'compania' => RecargasCompania::Telcel->value
        ];

        $this->post($this->_endpoint . '/recargas', $input)
            ->assertInvalid()
            ->assertSessionHasErrors('monto')
            ->assertSessionHasErrors('metodo_pago');
    }

    public function test_recargas_when_error_log_error()
    {
        $this->actingAs($this->_user);
        $input = [
            'monto' => fake()->numberBetween(10, 100),
            'metodo_pago' => MetodoPago::Efectivo->value,
            'compania' => 'Not specified'
        ];

        $this->post($this->_endpoint . '/recargas', $input)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Error al agregar registro',
            'modulo' => PagoServiciosRepository::class,
            'antes' => json_encode($input)
        ]);
    }


    public function test_servicios_when_called_saved_correctly_and_log_saved()
    {
        $this->actingAs($this->_user);
        $this->post('/caja/open', $this->_initCajaParams);
        $input = [
            'monto' => fake()->numberBetween(11, 77),
            'metodo_pago' => MetodoPago::Efectivo->value,
            'servicio' => fake()->text(25)
        ];

        $this->post($this->_endpoint . '/servicios', $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('servicios', $input);

        $input['comision'] = "15";
        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al agregar registro',
            'modulo' => PagoServiciosRepository::class,
            'despues' => json_encode($input)
        ]);
    }

    public function test_servicios_when_invalid_error_return_correctly()
    {
        $this->actingAs($this->_user);
        $input = [
            'monto' => 10,
            'servicio' => fake()->text
        ];

        $this->post($this->_endpoint . '/servicios', $input)
            ->assertInvalid()
            ->assertSessionHasErrors('metodo_pago');
    }

    public function test_servicios_when_error_log_error()
    {
        $this->actingAs($this->_user);
        $input = [
            'monto' => fake()->numberBetween(11, 77),
            'metodo_pago' => MetodoPago::Efectivo->value,
            'servicio' => fake()->text(1000) // More than allowed
        ];

        $this->post($this->_endpoint . '/servicios', $input)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $input['comision'] = "15";
        $this->assertDatabaseHas('log', [
            'descripcion' => 'Error al agregar registro',
            'modulo' => PagoServiciosRepository::class,
            'antes' => json_encode($input)
        ]);
    }
}
