<?php

namespace Tests\Unit\Classes;

use App\AperturaCaja;
use App\Classes\CierreAperturaCaja;
use App\Classes\CierreCaja\Contracts\IGastosCalculator;
use App\Classes\CierreCaja\Contracts\IServiciosRecargasCalculator;
use App\Classes\CierreCaja\Contracts\ITransferenciasCalculator;
use App\Classes\CierreCaja\Contracts\IVentasCalculator;
use App\Classes\CierreCajaGastos;
use App\Classes\CierreCajaServiciosRecargasComisiones;
use App\Classes\CierreCajaTransferencias;
use App\Classes\CierreCajaUtilidadesVentas;
use App\Repositories\IAperturasCajaRepository;
use Mockery;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;

class CierreAperturaCajaTest extends TestCase
{

    /**
     * @dataProvider _calculateEverythingTestCases
     */
    public function test_calculateEverything_happyPath(
        array $input,
        array $expected
    ) {
        $aperturaCajaRepository = Mockery::mock(IAperturasCajaRepository::class);
        $aperturaCajaMock = Mockery::mock(AperturaCaja::class);

        $ventasCalculatorStub = Mockery::mock(IVentasCalculator::class);
        $this->_prepareVentasCalculatorStub($ventasCalculatorStub, $input);

        $gastosCalculatorStub = Mockery::mock(IGastosCalculator::class);
        $this->_prepareGastosCalculatorStub($gastosCalculatorStub, $input);

        $serviciosRecargasCalculatorStub = Mockery::mock(IServiciosRecargasCalculator::class);
        $this->_prepareServiciosRecargasCalculatorStub(
            $serviciosRecargasCalculatorStub,
            $input
        );

        $transferenciasCalculatorStub = Mockery::mock(ITransferenciasCalculator::class);
        $this->_prepareTransferenciasCalculatorStub($transferenciasCalculatorStub, $input);

        $this->_prepareAperturaCajaMock(
            $aperturaCajaMock,
            $input
        );

        $aperturaCajaRepository
            ->shouldReceive('getCurrent')
            ->andReturn($aperturaCajaMock);

        $sut = new CierreAperturaCaja(
            $aperturaCajaRepository,
            $ventasCalculatorStub,
            $gastosCalculatorStub,
            $serviciosRecargasCalculatorStub,
            $transferenciasCalculatorStub
        );
        $actual = $sut->calculateEverything();

        $this->assertEquals($expected, $actual);
    }

    private function _prepareTransferenciasCalculatorStub(LegacyMockInterface $mock, array $input)
    {
        $cierreTransferencias = new CierreCajaTransferencias();
        $cierreTransferencias->ele_efe = $input['transferencias_ele_efe'];
        $cierreTransferencias->efe_ele = $input['transferencias_efe_ele'];

        $mock->shouldReceive('calculate')
            ->andReturn($cierreTransferencias);
    }

    private function _prepareServiciosRecargasCalculatorStub(LegacyMockInterface $mock, array $input)
    {
        $cierreRecargasServicios = new CierreCajaServiciosRecargasComisiones();
        $cierreRecargasServicios->servicios_recargas_efe = $input['servicios_recargas_efe'];
        $cierreRecargasServicios->servicios_recargas_ele = $input['servicios_recargas_ele'];
        $cierreRecargasServicios->comisiones_efe = $input['comisiones_efe'];
        $cierreRecargasServicios->comisiones_ele = $input['comisiones_ele'];

        $mock->shouldReceive('calculate')
            ->andReturn($cierreRecargasServicios);
    }

    private function _prepareGastosCalculatorStub(LegacyMockInterface $mock, array $input)
    {
        $cierreGastos = new CierreCajaGastos();
        $cierreGastos->gastos_apartados = $input['gastos_apartados'];
        $cierreGastos->apartados_dia = $input['apartados_dia'];
        $cierreGastos->gastos_efe = $input['gastos_efe'];
        $cierreGastos->gastos_ele = $input['gastos_ele'];
        $cierreGastos->gastos_recargas_servicios  = $input['gastos_recargas_servicios'];

        $mock->shouldReceive('calculate')
            ->andReturn($cierreGastos);
    }

    private function _prepareVentasCalculatorStub(
        LegacyMockInterface $mock,
        array $input
    ) {
        $cierreCajaVentas = new CierreCajaUtilidadesVentas();
        $cierreCajaVentas->ventas_efe = $input['ventas_efe'];
        $cierreCajaVentas->ventas_ele = $input['ventas_ele'] - $input['comision_mp'];
        $cierreCajaVentas->utilidades = $input['utilidades'] - $input['comision_mp'];

        $mock->shouldReceive('calculate')
            ->andReturn($cierreCajaVentas);
    }

    private function _prepareAperturaCajaMock(
        LegacyMockInterface $aperturaCajaMock,
        array $input
    ) {

        $aperturaCajaMock->shouldReceive('getAttribute')
            ->with('inicial_efe')
            ->andReturn($input['inicial_efe']);

        $aperturaCajaMock->shouldReceive('getAttribute')
            ->with('inicial_ele')
            ->andReturn($input['inicial_ele']);

        $aperturaCajaMock->shouldReceive('getAttribute')
            ->with('inicial_apartados')
            ->andReturn($input['inicial_apartados']);

        $aperturaCajaMock->shouldReceive('getAttribute')
            ->with('inicial_recargas_servicios')
            ->andReturn($input['inicial_recargas_servicios']);
    }

    private static function _calculateEverythingTestCases(): array
    {
        return [
            [
                'input' => [
                    'inicial_efe' => 1000,
                    'inicial_ele' => 200,
                    'inicial_apartados' => 1500,
                    'inicial_recargas_servicios' => 700,
                    'ventas_efe' => 1550,
                    'ventas_ele' => 675,
                    'comision_mp' => 19.29,
                    'servicios_recargas_efe' => 1800,
                    'comisiones_efe' => 48,
                    'servicios_recargas_ele' => 0,
                    'comisiones_ele' => 0,
                    'apartados_dia' => 500,
                    'utilidades' => 775,
                    'transferencias_efe_ele' => 0,
                    'transferencias_ele_efe' => 200,
                    'gastos_efe' => 1368,
                    'gastos_apartados' => 780,
                    'gastos_ele' => 10.0,
                    'gastos_recargas_servicios' => 200,
                ],
                'expected' => [
                    'gastos_efe' => 1368,
                    'apartados_dia' => 500,
                    'gastos_apartados' => 780,
                    'gastos_ele' => 10.0,
                    'gastos_recargas_servicios' => 200,
                    'ventas_efe' => 1550,
                    // input[ventas_ele] - comision_mp
                    'ventas_ele' => 655.71,
                    'servicios_recargas_efe' => 1848,

                    // input[utilidades] - comision_mp + comisiones_efe + comisiones_ele
                    'subtotal_efe' => 882.0,
                    'subtotal_ele' => 645.71,
                    'subtotal_apartados' => 1220.0,
                    'subtotal_recargas_servicios' => 2348.0,
                    'total' => 1527.71,
                    'utilidades' => 803.71,
                ]
            ],
            [
                'input' => [
                    'inicial_efe' => 1758.5,
                    'inicial_ele' => 3185,
                    'inicial_apartados' => 3198.6,
                    'inicial_recargas_servicios' => 2006,
                    'ventas_efe' => 1000,
                    'ventas_ele' => 475,
                    'comision_mp' => 19.29,
                    'servicios_recargas_efe' => 948,
                    'comisiones_efe' => 48,
                    'servicios_recargas_ele' => 0,
                    'comisiones_ele' => 0,
                    'apartados_dia' => 500,
                    'utilidades' => 475,
                    'transferencias_efe_ele' => 150,
                    'transferencias_ele_efe' => 500,
                    'gastos_efe' => 1258,
                    'gastos_ele' => 0,
                    'gastos_apartados' => 0,
                    'gastos_recargas_servicios' => 300,
                ],
                'expected' => [
                    'gastos_efe' => 1258,
                    'gastos_ele' => 0,
                    'apartados_dia' => 500,
                    'gastos_apartados' => 0,
                    'gastos_recargas_servicios' => 300,
                    'ventas_efe' => 1000,
                    'ventas_ele' => 455.71,
                    'servicios_recargas_efe' => 996,

                    'subtotal_efe' => 1350.5,
                    'subtotal_ele' => 3290.71,
                    'subtotal_apartados' => 3698.6,
                    'subtotal_recargas_servicios' => 2702.0,
                    'total' => 4641.21,
                    'utilidades' => 503.71,
                ]
            ],
            [
                'input' => [
                    'inicial_efe' => 1356,
                    'inicial_ele' => 3333,
                    'inicial_apartados' => 2567.5,
                    'inicial_recargas_servicios' => 2006,
                    'ventas_efe' => 1145,
                    'ventas_ele' => 450,
                    'comision_mp' => 14.21,
                    'servicios_recargas_efe' => 1000,
                    'comisiones_efe' => 25,
                    'servicios_recargas_ele' => 100,
                    'comisiones_ele' => 1,
                    'apartados_dia' => 800,
                    'utilidades' => 650,
                    'transferencias_efe_ele' => 500,
                    'transferencias_ele_efe' => 1100,
                    'gastos_efe' => 780,
                    'gastos_ele' => 600,
                    'gastos_apartados' => 1200,
                    'gastos_recargas_servicios' => 1100
                ],
                'expected' => [
                    'gastos_efe' => 780,
                    'gastos_ele' => 600,
                    'apartados_dia' => 800,
                    'gastos_apartados' => 1200,
                    'gastos_recargas_servicios' => 1100,
                    'ventas_efe' => 1145,
                    'ventas_ele' => 435.79,
                    'servicios_recargas_efe' => 1025,

                    'subtotal_efe' => 1521,
                    'subtotal_ele' => 2669.79,
                    'total' => 4190.79,
                    'subtotal_recargas_servicios' => 1931,
                    'subtotal_apartados' => 2167.5,
                    'utilidades' => 661.79,
                ]
            ]
        ];
    }
}
