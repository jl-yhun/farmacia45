<?php

namespace Tests\Feature;

use App\AperturaCaja;
use App\User;
use Database\Seeders\AperturasCajaControllerTestSeed;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AperturasCajaControllerTest extends TestCase
{
    private $_endpoint = '/api/aperturas-caja';
    private $_user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AperturasCajaControllerTestSeed::class);
        $this->_user = User::find(1);
    }

    public function test_index_when_called_return_correct_list()
    {
        $aperturas = AperturaCaja::all()->toArray();
        $this->actingAs($this->_user);

        $this->get($this->_endpoint)
            ->assertOk()
            ->assertJsonPath('estado', true)
            ->assertJson(function (AssertableJson $json) use ($aperturas) {
                $json->etc()
                    ->has('data.2', function ($okJson) use ($aperturas) {
                        $okJson
                            ->where('inicial_efe', $aperturas[0]['inicial_efe'])
                            ->where('inicial_ele', $aperturas[0]['inicial_ele'])
                            ->where('inicial_apartados', $aperturas[0]['inicial_apartados'])
                            ->where('ventas_efe', $aperturas[0]['ventas_efe'])
                            ->where('ventas_ele', $aperturas[0]['ventas_ele'])
                            ->where('utilidades', $aperturas[0]['utilidades'])
                            ->where('gastos_efe', $aperturas[0]['gastos_efe'])
                            ->where('gastos_ele', $aperturas[0]['gastos_ele'])
                            ->where('servicios_recargas_efe', $aperturas[0]['servicios_recargas_efe'])
                            ->where('servicios_recargas_ele', $aperturas[0]['servicios_recargas_ele'])
                            ->where('total', $aperturas[0]['total'])
                            ->where('subtotal_efe', $aperturas[0]['subtotal_efe'])
                            ->where('subtotal_ele', $aperturas[0]['subtotal_ele'])
                            ->where('subtotal_apartados', $aperturas[0]['subtotal_apartados'])
                            ->where('fecha_hora_cierre', $aperturas[0]['fecha_hora_cierre'])
                            ->where('estado', $aperturas[0]['estado'])
                            ->where('observaciones', $aperturas[0]['observaciones'])
                            ->etc();
                    })
                    ->has('data.1', function ($okJson) use ($aperturas) {
                        $okJson
                            ->where('inicial_efe', $aperturas[1]['inicial_efe'])
                            ->where('inicial_ele', $aperturas[1]['inicial_ele'])
                            ->where('inicial_apartados', $aperturas[1]['inicial_apartados'])
                            ->where('ventas_efe', $aperturas[1]['ventas_efe'])
                            ->where('ventas_ele', $aperturas[1]['ventas_ele'])
                            ->where('utilidades', $aperturas[1]['utilidades'])
                            ->where('gastos_efe', $aperturas[1]['gastos_efe'])
                            ->where('gastos_ele', $aperturas[1]['gastos_ele'])
                            ->where('servicios_recargas_efe', $aperturas[1]['servicios_recargas_efe'])
                            ->where('servicios_recargas_ele', $aperturas[1]['servicios_recargas_ele'])
                            ->where('total', $aperturas[1]['total'])
                            ->where('subtotal_efe', $aperturas[1]['subtotal_efe'])
                            ->where('subtotal_ele', $aperturas[1]['subtotal_ele'])
                            ->where('subtotal_apartados', $aperturas[1]['subtotal_apartados'])
                            ->where('fecha_hora_cierre', $aperturas[1]['fecha_hora_cierre'])
                            ->where('estado', $aperturas[1]['estado'])
                            ->where('observaciones', $aperturas[1]['observaciones'])
                            ->etc();
                    })
                    ->has('data.0', function ($okJson) use ($aperturas) {
                        $okJson
                            ->where('inicial_efe', $aperturas[2]['inicial_efe'])
                            ->where('inicial_ele', $aperturas[2]['inicial_ele'])
                            ->where('inicial_apartados', $aperturas[2]['inicial_apartados'])
                            ->where('ventas_efe', '0.00')
                            ->where('ventas_ele', '0.00')
                            ->where('utilidades', '0.00')
                            ->where('gastos_efe', '0.00')
                            ->where('gastos_ele', '0.00')
                            ->where('servicios_recargas_efe', '0.00')
                            ->where('servicios_recargas_ele', '0.00')
                            ->where('total', '0.00')
                            ->where('subtotal_efe', '0.00')
                            ->where('subtotal_ele', '0.00')
                            ->where('subtotal_apartados', '0.00')
                            ->where('fecha_hora_cierre', $aperturas[2]['fecha_hora_cierre'])
                            ->where('estado', $aperturas[2]['estado'])
                            ->where('observaciones', $aperturas[2]['observaciones'])
                            ->etc();
                    })
                    ->etc();
            });
    }

    public function test_index_when_no_permissions_return_correct_status()
    {
        $this->actingAs(User::find(2));

        $this->get($this->_endpoint)
            ->assertForbidden();
    }
}
