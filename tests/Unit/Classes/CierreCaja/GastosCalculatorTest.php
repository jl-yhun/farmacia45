<?php

namespace Tests\Unit\Classes\CierreCaja;

use App\Apartado;
use App\AperturaCaja;
use App\Classes\CierreCaja\Implementations\GastosCalculator;
use App\Classes\CierreCaja\Implementations\VentasCalculator;
use App\Classes\CierreCajaGastos;
use App\Classes\CierreCajaUtilidadesVentas;
use App\Enums\Config;
use App\Enums\FuenteGasto;
use App\Enums\MetodoPago;
use App\Gasto;
use App\Repositories\IAperturasCajaRepository;
use App\Repositories\IConfiguracionRepository;
use App\Venta;
use Mockery;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;

class GastosCalculatorTest extends TestCase
{
    /**
     * @dataProvider _calculateTestCases
     */
    public function test_calculate_happyPath(array $gastos, array $apartados, array $expected)
    {
        $aperturaCajaRepositoryStub = Mockery::mock(IAperturasCajaRepository::class);
        $aperturaCajaStub = Mockery::mock(AperturaCaja::class);

        $this->_prepareAperturaCajaStub(
            $aperturaCajaStub,
            $gastos,
            $apartados
        );

        $aperturaCajaRepositoryStub
            ->shouldReceive('getCurrent')
            ->andReturn($aperturaCajaStub);

        $actor = new GastosCalculator($aperturaCajaRepositoryStub);
        $actual = $actor->calculate();

        $this->assertEquals($expected['gastos_efe'], $actual->gastos_efe);
        $this->assertEquals($expected['gastos_ele'], $actual->gastos_ele);
        $this->assertEquals($expected['gastos_apartados'], $actual->gastos_apartados);
        $this->assertEquals($expected['gastos_recargas_servicios'], $actual->gastos_recargas_servicios);
        $this->assertEquals($expected['apartados_dia'], $actual->apartados_dia);
    }

    private function _prepareAperturaCajaStub(
        LegacyMockInterface $aperturaCajaFake,
        array $gastosArray,
        array $apartadosArray
    ) {
        $aperturaCajaFake->shouldReceive('getAttribute')
            ->with('gastos')
            ->andReturn($this->_buildDummyGastosModelArray($gastosArray));

        $aperturaCajaFake->shouldReceive('getAttribute')
            ->with('apartados')
            ->andReturn($this->_buildDummyApartadosModelArray($apartadosArray));
    }

    private function _buildDummyApartadosModelArray(array $apartadosArray): array
    {
        $arrayResult = [];
        foreach ($apartadosArray as $item) {
            $apartadoFake = Mockery::mock(Apartado::class);

            $apartadoFake->shouldReceive('getAttribute')
                ->with('monto')
                ->andReturn($item);

            $arrayResult[] = $apartadoFake;
        }
        return $arrayResult;
    }

    private function _buildDummyGastosModelArray(array $gastosArray): array
    {
        $gastosArrayResult = [];
        foreach ($gastosArray as $gastoItem) {
            $gastoFake = Mockery::mock(Gasto::class);
            $gastoFake->shouldReceive('getAttribute')
                ->with('fuente')
                ->andReturn($gastoItem['fuente']);

            $gastoFake->shouldReceive('getAttribute')
                ->with('monto')
                ->andReturn($gastoItem['monto']);

            $gastosArrayResult[] = $gastoFake;
        }
        return $gastosArrayResult;
    }

    private static function _calculateTestCases(): array
    {
        return [
            [
                'gastos' => [
                    [
                        'fuente' => FuenteGasto::Caja->value,
                        'monto' => 1000
                    ],
                    [
                        'fuente' => FuenteGasto::MercadoPago->value,
                        'monto' => 2.5
                    ],
                    [
                        'fuente' => FuenteGasto::MercadoPago->value,
                        'monto' => 7.5
                    ],
                    [
                        'fuente' => FuenteGasto::Caja->value,
                        'monto' => 368
                    ],
                    [
                        'fuente' => FuenteGasto::RecargasServicios->value,
                        'monto' => 200
                    ]
                ],
                'apartados' => [-200, -580, 500],
                'expected' => [
                    'apartados_dia' => 500,
                    'gastos_efe' => 1368,
                    'gastos_apartados' => 780,
                    'gastos_ele' => 10.0,
                    'gastos_recargas_servicios' => 200,
                ]
            ],
            [
                'gastos' => [
                    [
                        'fuente' => FuenteGasto::Caja->value,
                        'monto' => 1258
                    ],
                    [
                        'fuente' => FuenteGasto::RecargasServicios->value,
                        'monto' => 300
                    ]
                ],
                'apartados' => [500],
                'expected' => [
                    'apartados_dia' => 500,
                    'gastos_efe' => 1258,
                    'gastos_ele' => 0,
                    'gastos_apartados' => 0,
                    'gastos_recargas_servicios' => 300,
                ]
            ]
        ];
    }
}
