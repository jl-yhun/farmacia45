<?php

namespace Tests\Unit\Classes\CierreCaja;

use App\AperturaCaja;
use App\Classes\CierreCaja\Implementations\VentasCalculator;
use App\Classes\CierreCajaUtilidadesVentas;
use App\Enums\Config;
use App\Enums\MetodoPago;
use App\Repositories\IAperturasCajaRepository;
use App\Repositories\IConfiguracionRepository;
use App\Venta;
use Mockery;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;

class VentasCalculatorTest extends TestCase
{
    /**
     * @dataProvider _calculateTestCases
     */
    public function test_calculate_happyPath(array $ventas, array $expected)
    {
        $aperturaCajaRepositoryStub = Mockery::mock(IAperturasCajaRepository::class);
        $configuracionRepositoryStub = Mockery::mock(IConfiguracionRepository::class);
        $aperturaCajaStub = Mockery::mock(AperturaCaja::class);

        $cierreVentasUtilidadesMock = Mockery::mock(CierreCajaUtilidadesVentas::class);

        $this->_prepareCierreVentasMock($cierreVentasUtilidadesMock, $expected);

        $this->_prepareConfigRepoMock($configuracionRepositoryStub);
        $this->_prepareAperturaCajaStub(
            $aperturaCajaStub,
            $ventas
        );

        $aperturaCajaRepositoryStub
            ->shouldReceive('getCurrent')
            ->andReturn($aperturaCajaStub);

        $actor = new VentasCalculator($aperturaCajaRepositoryStub, $configuracionRepositoryStub);
        $actual = $actor->calculate();

        $this->assertEquals($expected['ventas_efe'], $actual->ventas_efe);
        $this->assertEquals($expected['ventas_ele'], $actual->ventas_ele);
        $this->assertEquals($expected['utilidades'], $actual->utilidades);
    }

    private function _prepareConfigRepoMock(LegacyMockInterface $mock)
    {
        $mock->shouldReceive('get')
            ->with(Config::MercadoPagoPoint)
            ->andReturn(.0406);

        $mock->shouldReceive('get')
            ->with(Config::ComisionRecargas)
            ->andReturn(1);

        $mock->shouldReceive('get')
            ->with(Config::ComisionServicios)
            ->andReturn(15);
    }

    private function _prepareCierreVentasMock(LegacyMockInterface $mock, array $expected)
    {
        $mock->shouldReceive('getAttribute')
            ->with('ventas_ele')
            ->andReturn($expected['ventas_ele']);

        $mock->shouldReceive('getAttribute')
            ->with('ventas_efe')
            ->andReturn($expected['ventas_efe']);

        $mock->shouldReceive('getAttribute')
            ->with('utilidades')
            ->andReturn($expected['utilidades']);
    }

    private function _prepareAperturaCajaStub(
        LegacyMockInterface $aperturaCajaFake,
        array $ventasArray
    ) {
        $aperturaCajaFake->shouldReceive('getAttribute')
            ->with('ventas')
            ->andReturn($this->_buildDummyVentasModelArray($ventasArray));
    }

    private function _buildDummyVentasModelArray(array $ventasArray): array
    {
        $ventasArrayResult = [];
        foreach ($ventasArray as $ventaItem) {
            $ventaFake = Mockery::mock(Venta::class);
            $ventaFake->shouldReceive('getAttribute')
                ->with('utilidad')
                ->andReturn($ventaItem['utilidad']);

            $ventaFake->shouldReceive('getAttribute')
                ->with('total')
                ->andReturn($ventaItem['total']);

            $ventaFake->shouldReceive('getAttribute')
                ->with('metodo_pago')
                ->andReturn($ventaItem['metodo_pago']);

            $ventaFake->shouldReceive('getAttribute')
                ->with('hasCancelaciones')
                ->andReturn($ventaItem['hasCancelaciones']);

            $ventasArrayResult[] = $ventaFake;
        }
        return $ventasArrayResult;
    }

    private static function _calculateTestCases(): array
    {
        return [
            [
                'ventas' => [
                    [
                        'utilidad' => 140,
                        'total' => 400,
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 50,
                        'total' => 150,
                        'metodo_pago' => MetodoPago::TarjetaDebito->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 190,
                        'total' => 600,
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 95,
                        'total' => 325,
                        'metodo_pago' => MetodoPago::TarjetaDebito->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 100,
                        'total' => 350,
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 100,
                        'total' => 200,
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 100,
                        'total' => 200,
                        'metodo_pago' => MetodoPago::Transferencia->value,
                        'hasCancelaciones' => false
                    ],
                ],
                'expected' => [
                    'ventas_efe' => 1550,
                    'ventas_ele' => 655.72,
                    'utilidades' => 755.72,
                ]
            ],
            [
                'ventas' => [
                    [
                        'utilidad' => 140,
                        'total' => 400,
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 50,
                        'total' => 150,
                        'metodo_pago' => MetodoPago::TarjetaDebito->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 190,
                        'total' => 600,
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'utilidad' => 95,
                        'total' => 325,
                        'metodo_pago' => MetodoPago::TarjetaDebito->value,
                        'hasCancelaciones' => false
                    ],
                ],
                'expected' => [
                    'ventas_efe' => 1000,
                    'ventas_ele' => 455.72,
                    'utilidades' => 455.72
                ]
            ],
            [
                'ventas' => [
                    [
                        'total' => 600,
                        'utilidad' => 333,
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'total' => 800,
                        'utilidad' => 200,
                        'metodo_pago' => MetodoPago::TarjetaDebito->value,
                        'hasCancelaciones' => false
                    ],
                    [
                        'total' => 900,
                        'utilidad' => 100,
                        'metodo_pago' => MetodoPago::Efectivo->value,
                        'hasCancelaciones' => true
                    ],
                    [
                        'total' => 780,
                        'utilidad' => 123,
                        'metodo_pago' => MetodoPago::Transferencia->value,
                        'hasCancelaciones' => false
                    ],
                ],
                'expected' => [
                    'ventas_efe' => 600,
                    'ventas_ele' =>  1547.52,
                    'utilidades' => 623.52
                ]
            ]
        ];
    }
}
