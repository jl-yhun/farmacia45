<?php

namespace Tests\Unit;

use App\Classes\PurchaseCalculator;
use App\Producto;
use PHPUnit\Framework\TestCase;

class PurchaseCalculatorTest extends TestCase
{
    public $_productoMock;

    protected function setUp(): void
    {
        $this->_productoMock = $this->getMockBuilder(Producto::class)
            ->onlyMethods(['getIsGranelAttribute', 'getBestStockAttribute', 'getUnidadesPaqueteAttribute'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider testCases_calculateAmountForPurchase
     */
    public function test_calculateAmountForPurchase(
        $minStock,
        $maxStock,
        $unidadesPorPaquete,
        $bestStock,
        $isGranel,
        $expected
    ) {
        $this->_productoMock->min_stock = $minStock;
        $this->_productoMock->max_stock = $maxStock;

        $this->_productoMock->method('getUnidadesPaqueteAttribute')
            ->willReturn($unidadesPorPaquete);
        $this->_productoMock->method('getBestStockAttribute')
            ->willReturn($bestStock);
        $this->_productoMock->method('getIsGranelAttribute')
            ->willReturn($isGranel);

        $actual = PurchaseCalculator::calculateAmountForPurchase($this->_productoMock);

        $this->assertEquals($expected, $actual);
    }

    private function testCases_calculateAmountForPurchase(): array
    {
        return [
            [4, 21, 3, null, true, 6],
            [4, 21, 4, null, true, 5],
            [4, 16, 4, null, true, 4],
            [0, 0, 0, 2, false, 2]
        ];
    }
}
