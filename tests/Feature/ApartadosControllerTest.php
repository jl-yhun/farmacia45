<?php

namespace Tests\Feature;

use App\Apartado;
use App\Repositories\ApartadosRepository;
use App\User;
use Database\Seeders\Apartados\TestingSeeder;
use Tests\TestCase;

class ApartadosControllerTest extends TestCase
{
    private $_endpoint = '/apartados';
    private $_user;
    private $_cajaInicialParams = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
        $this->_user = User::find(1);
        $this->_cajaInicialParams = [
            'inicial_efe' => 1000,
            'inicial_ele' => 200,
            'inicial_apartados' => 1500,
            'inicial_recargas_servicios' => 100
        ];
    }

    public function test_index_when_admin_return_correct_json()
    {
        $this->actingAs($this->_user);

        $expected = Apartado::orderBy('id', 'desc')->take(50)->get();

        $this->get('/api' . $this->_endpoint)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data', $expected->toArray());
    }

    public function test_index_when_user_return_correct_json()
    {
        $user = User::find(2);
        $this->actingAs($user);

        $expected = Apartado::where('usuario_id', 2)->orderBy('id', 'desc')->take(50)->get();

        $this->get('/api' . $this->_endpoint)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJsonPath('data', $expected->toArray());
    }

    public function test_index_when_no_auth_return_correct_result()
    {
        $user = User::find(3); // no auth
        $this->actingAs($user);

        $this->get('/api' . $this->_endpoint)
            ->assertForbidden();
    }



    public function test_store_when_called_saved_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $this->post('/caja/open', $this->_cajaInicialParams);

        $input = [
            'monto' => 311,
            'concepto' => fake()->text(100)
        ];

        $this->post('/api' . $this->_endpoint, $input)
            ->assertOk()
            ->assertJsonPath('estado', true);

        $this->assertDatabaseHas('apartados', $input);

        $this->assertDatabaseHas('log', [
            'modulo' => ApartadosRepository::class,
            'despues' => json_encode($input)
        ]);
    }

    public function test_store_when_not_amount_available_return_error_and_log_saved()
    {
        $this->actingAs($this->_user);

        $this->post('/caja/open', $this->_cajaInicialParams);

        $current = Apartado::sum('monto');

        $input = [
            'monto' => - ($current + 10),
            'concepto' => fake()->text(100)
        ];

        $this->post('/api' . $this->_endpoint, $input)
            ->assertServerError();

        $this->assertDatabaseHas('log', [
            'modulo' => ApartadosRepository::class,
            'descripcion' => 'Se intentó exceder apartados.',
            'antes' => json_encode($input)
        ]);

        $this->assertDatabaseMissing('apartados', $input);
    }

    public function test_store_when_invalid_return_correct_errors()
    {
        $this->actingAs($this->_user);

        $this->post('/caja/open', $this->_cajaInicialParams);

        $input = [
            'monto' => 0
        ];

        $this->post('/api' . $this->_endpoint, $input)
            ->assertInvalid()
            ->assertSessionHasErrors(['monto', 'concepto']);
    }

    public function test_store_when_error_return_error_and_log_recorded()
    {
        $this->actingAs($this->_user);

        $this->post('/caja/open', $this->_cajaInicialParams);

        $input = [
            'monto' => 'Expected a number here',
            'concepto' => fake()->text(100)
        ];

        $this->post('/api' . $this->_endpoint, $input)
            ->assertOk()
            ->assertJsonPath('estado', false);

        $this->assertDatabaseHas('log', [
            'modulo' => ApartadosRepository::class,
            'antes' => json_encode($input)
        ]);
    }

    public function test_store_when_not_authorized_return_correct_error()
    {
        $user = User::find(2);
        $this->actingAs($user);

        $this->post('/caja/open', $this->_cajaInicialParams);

        $input = [
            'monto' => 311,
            'concepto' => fake()->text(100)
        ];

        $this->post('/api' . $this->_endpoint, $input)
            ->assertForbidden();
    }

    public function test_store_when_not_authenticated_redirect_login()
    {
        $input = [
            'monto' => 311,
            'concepto' => fake()->text(100)
        ];

        $this->post('/api' . $this->_endpoint, $input)
            ->assertRedirectToRoute('login');
    }
}
