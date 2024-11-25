<?php

namespace Tests\Feature;

use App\Enums\TipoTransferencia;
use App\Repositories\TransferenciasRepository;
use App\User;
use Database\Seeders\TransferenciasTestSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransferenciasControllerTest extends TestCase
{
    private $_user;
    private $_endpoint = '/api/transferencias';

    protected function setUp(): void
    {
        parent::setUp();
        $this->_user = User::find(1);
        $this->seed(TransferenciasTestSeeder::class);
    }

    public function test_store_when_called_saved_correctly_and_log_saved()
    {
        $this->actingAs($this->_user);
        $this->post('/caja/open', [
            'inicial_efe' => 1000, 'inicial_ele' => 0,
            'inicial_apartados' => 1500,
            'inicial_recargas_servicios' => 700
        ]);

        $input = [
            'monto' => 10,
            'concepto' => fake()->realText(200),
            'tipo' => TipoTransferencia::EleEfe->value
        ];

        $this->post($this->_endpoint, $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('transferencias', $input);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Ok al agregar registro',
            'modulo' => TransferenciasRepository::class,
            'despues' => json_encode($input)
        ]);
    }

    public function test_store_when_invalid_error_return_correctly()
    {
        $this->actingAs($this->_user);
        $input = [
            'monto' => 10
        ];

        $this->post($this->_endpoint, $input)
            ->assertInvalid()
            ->assertSessionHasErrors(['concepto', 'tipo']);
    }

    public function test_store_when_error_log_error()
    {
        $this->actingAs($this->_user);
        $input = [
            'monto' => 'Expected a Number',
            'concepto' => fake()->realText(200),
            'tipo' => TipoTransferencia::EfeEle->value
        ];

        $this->post($this->_endpoint, $input)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'descripcion' => 'Error al agregar registro',
            'modulo' => TransferenciasRepository::class,
            'antes' => json_encode($input)
        ]);
    }
}
