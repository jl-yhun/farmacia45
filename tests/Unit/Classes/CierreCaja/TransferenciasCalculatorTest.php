<?php

namespace Tests\Unit\Classes\CierreCaja;

use App\AperturaCaja;
use App\Classes\CierreCaja\Implementations\TransferenciasCalculator;
use App\Enums\MetodoPago;
use App\Enums\TipoTransferencia;
use App\Repositories\IAperturasCajaRepository;
use App\Transferencia;
use Mockery;
use Mockery\LegacyMockInterface;
use PHPUnit\Framework\TestCase;

class TransferenciasCalculatorTest extends TestCase
{
    /**
     * @dataProvider _calculateTestCases
     */
    public function test_calculate_happyPath(array $ventas, array $expected)
    {
        $aperturaCajaRepositoryStub = Mockery::mock(IAperturasCajaRepository::class);
        $aperturaCajaStub = Mockery::mock(AperturaCaja::class);

        $this->_prepareAperturaCajaStub(
            $aperturaCajaStub,
            $ventas
        );

        $aperturaCajaRepositoryStub
            ->shouldReceive('getCurrent')
            ->andReturn($aperturaCajaStub);

        $actor = new TransferenciasCalculator($aperturaCajaRepositoryStub);
        $actual = $actor->calculate();

        $this->assertEquals($expected['ele_efe'], $actual->ele_efe);
        $this->assertEquals($expected['efe_ele'], $actual->efe_ele);
    }

    private function _prepareAperturaCajaStub(LegacyMockInterface $mock, array $transferenciasArray)
    {
        $mock->shouldReceive('getAttribute')
            ->with('transferencias')
            ->andReturn($this->_buildDummyTransferenciasModelArray($transferenciasArray));
    }

    private function _buildDummyTransferenciasModelArray(array $array): array
    {
        $arrayResult = [];
        foreach ($array as $item) {
            $fake = Mockery::mock(Transferencia::class);

            $fake->shouldReceive('getAttribute')
                ->with('monto')
                ->andReturn($item['monto']);

            $fake->shouldReceive('getAttribute')
                ->with('tipo')
                ->andReturn($item['tipo']);

            $arrayResult[] = $fake;
        }
        return $arrayResult;
    }

    private static function _calculateTestCases(): array
    {
        return [
            [
                'transferencias' => [
                    [
                        'tipo' => TipoTransferencia::EleEfe->value,
                        'monto' => 200
                    ]
                ],
                'expected' => [
                    'ele_efe' => 200,
                    'efe_ele' => 0
                ]
            ],
            [
                'transferencias' => [
                    [
                        'tipo' => TipoTransferencia::EleEfe->value,
                        'monto' => 500
                    ],
                    [
                        'tipo' => TipoTransferencia::EfeEle->value,
                        'monto' => 150
                    ]
                ],
                'expected' => [
                    'ele_efe' => 500,
                    'efe_ele' => 150
                ]
            ]
        ];
    }
}
