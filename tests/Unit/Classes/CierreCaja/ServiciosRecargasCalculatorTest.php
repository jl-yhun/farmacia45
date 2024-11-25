<?php

namespace Tests\Unit\Classes\CierreCaja;

use App\Apartado;
use App\AperturaCaja;
use App\Classes\CierreCaja\Implementations\GastosCalculator;
use App\Classes\CierreCaja\Implementations\ServiciosRecargasCalculator;
use App\Classes\CierreCaja\Implementations\VentasCalculator;
use App\Classes\CierreCajaGastos;
use App\Classes\CierreCajaUtilidadesVentas;
use App\Enums\Config;
use App\Enums\FuenteGasto;
use App\Enums\MetodoPago;
use App\Gasto;
use App\Recarga;
use App\Repositories\IAperturasCajaRepository;
use App\Repositories\IConfiguracionRepository;
use App\Servicio;
use App\Venta;
use Mockery;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;

class ServiciosRecargasCalculatorTest extends TestCase
{
    /**
     * @dataProvider _calculateTestCases
     */
    public function test_calculate_happyPath(array $servicios, array $recargas, array $expected)
    {
        $aperturaCajaRepositoryStub = Mockery::mock(IAperturasCajaRepository::class);
        $aperturaCajaStub = Mockery::mock(AperturaCaja::class);
        $configuracionRepositoryMock = Mockery::mock(IConfiguracionRepository::class);

        $this->_prepareAperturaCajaStub(
            $aperturaCajaStub,
            $servicios,
            $recargas
        );

        $aperturaCajaRepositoryStub
            ->shouldReceive('getCurrent')
            ->andReturn($aperturaCajaStub);

        $this->_prepareConfigRepoMock($configuracionRepositoryMock);

        $actor = new ServiciosRecargasCalculator($aperturaCajaRepositoryStub, $configuracionRepositoryMock);
        $actual = $actor->calculate();

        $this->assertEquals($expected['servicios_recargas_efe'], $actual->servicios_recargas_efe);
        $this->assertEquals($expected['servicios_recargas_ele'], $actual->servicios_recargas_ele);
        $this->assertEquals($expected['comisiones_efe'], $actual->comisiones_efe);
        $this->assertEquals($expected['comisiones_ele'], $actual->comisiones_ele);
    }

    private function _prepareConfigRepoMock(LegacyMockInterface $mock)
    {
        $mock->shouldReceive('get')
            ->with(Config::ComisionRecargas)
            ->andReturn(1);
    }

    private function _prepareAperturaCajaStub(
        LegacyMockInterface $aperturaCajaFake,
        array $serviciosArray,
        array $recargasArray
    ) {
        $aperturaCajaFake->shouldReceive('getAttribute')
            ->with('servicios')
            ->andReturn($this->_buildDummyServiciosModelArray($serviciosArray));

        $aperturaCajaFake->shouldReceive('getAttribute')
            ->with('recargas')
            ->andReturn($this->_buildDummyRecargasModelArray($recargasArray));
    }

    private function _buildDummyRecargasModelArray(array $array): array
    {
        $arrayResult = [];
        foreach ($array as $item) {
            $fake = Mockery::mock(Recarga::class);

            $fake->shouldReceive('getAttribute')
                ->with('monto')
                ->andReturn($item['monto']);

            $fake->shouldReceive('getAttribute')
                ->with('metodo_pago')
                ->andReturn($item['metodo_pago']);

            $arrayResult[] = $fake;
        }
        return $arrayResult;
    }

    private function _buildDummyServiciosModelArray(array $array): array
    {
        $arrayResult = [];
        foreach ($array as $item) {
            $fake = Mockery::mock(Servicio::class);

            $fake->shouldReceive('getAttribute')
                ->with('monto')
                ->andReturn($item['monto']);

            $fake->shouldReceive('getAttribute')
                ->with('metodo_pago')
                ->andReturn($item['metodo_pago']);

            $fake->shouldReceive('getAttribute')
                ->with('comision')
                ->andReturn($item['comision']);

            $arrayResult[] = $fake;
        }
        return $arrayResult;
    }

    private static function _calculateTestCases(): array
    {
        return [
            [
                'servicios' => [
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 1200,
                        'comision' => 15
                    ],
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 500,
                        'comision' => 15
                    ],
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 50,
                        'comision' => 15
                    ],
                ],
                'recargas' => [
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 10
                    ],
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 20
                    ],
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 20
                    ],
                ],
                'expected' => [
                    'servicios_recargas_efe' => 1800,
                    'servicios_recargas_ele' => 0,
                    'comisiones_efe' => 48,
                    'comisiones_ele' => 0
                ]
            ],
            [
                'servicios' => [
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 100,
                        'comision' => 15
                    ],
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 758,
                        'comision' => 15
                    ],
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 55,
                        'comision' => 15
                    ],
                ],
                'recargas' => [
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 10
                    ],
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 10
                    ],
                    [
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'monto' => 15
                    ],
                ],
                'expected' => [
                    'servicios_recargas_efe' => 948,
                    'servicios_recargas_ele' => 0,
                    'comisiones_efe' => 48,
                    'comisiones_ele' => 0
                ]
            ]
        ];
    }
}
