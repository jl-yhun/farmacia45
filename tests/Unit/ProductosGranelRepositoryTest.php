<?php

namespace Tests\Unit;

use App\Classes\IStockManager;
use App\Exceptions\StockLessThanZeroException;
use App\Helpers\LoggerBuilder;
use App\Helpers\SanitizerBuilder;
use App\Producto;
use App\ProductoGranel;
use App\Repositories\IProductosGranelRepository;
use App\Repositories\ProductosGranelRepository;
use App\Repositories\ProductosRepository;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProductosGranelRepositoryTest extends TestCase
{
    private $_loggerBuilderMock;
    private $_productoMock;
    private $_productoGranelModelMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_loggerBuilderMock = $this->createMock(LoggerBuilder::class);
        $this->_productoMock = $this->getMockBuilder(Producto::class)
            ->onlyMethods(['producto_granel', 'save'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->_productoGranelModelMock = $this->getMockBuilder(ProductoGranel::class)
            ->onlyMethods(['delete'])
            ->addMethods(['updateOrCreate'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @dataProvider testCases_tryCreateProductoGranel
     */
    public function test_tryCreateProductoGranel_happy_path($isGranel, $timesExpected)
    {
        $this->_productoMock->producto_granel = null;

        $this->_productoMock
            ->method('producto_granel')
            ->willReturn($this->_productoGranelModelMock);

        $this->_productoGranelModelMock
            ->expects($this->exactly($timesExpected))
            ->method('updateOrCreate');

        $obj = new ProductosGranelRepository(
            $this->_loggerBuilderMock
        );
        $obj->tryCreateProductoGranel($this->_productoMock, [
            'isGranel' => $isGranel,
            'unidades_paquete' => 1
        ]);
    }

    public function test_tryCreateProductoGranel_NoGranelInputButActuallyGranel()
    {
        $this->_productoMock->producto_granel = new stdClass;

        $this->_productoMock
            ->method('producto_granel')
            ->willReturn($this->_productoGranelModelMock);

        $this->_productoGranelModelMock
            ->expects($this->exactly(0))
            ->method('delete');

        $obj = new ProductosGranelRepository(
            $this->_loggerBuilderMock
        );
        $obj->tryCreateProductoGranel($this->_productoMock, [
            'stock' => 1
        ]);
    }

    public function test_tryCreateProductoGranel_when_removed()
    {
        $this->_productoMock->producto_granel = $this->_productoGranelModelMock;

        $this->_productoMock
            ->method('producto_granel')
            ->willReturn($this->_productoGranelModelMock);

        $this->_productoGranelModelMock
            ->expects($this->exactly(1))
            ->method('delete');

        $obj = new ProductosGranelRepository(
            $this->_loggerBuilderMock
        );
        $obj->tryCreateProductoGranel($this->_productoMock, [
            'isGranel' => 0,
            'unidades_paquete' => 1
        ]);
    }

    private function testCases_tryCreateProductoGranel(): array
    {
        return [
            [true, 1],
            [1, 1],
            ['1', 1],
            ['true', 1],
            ['', 0],
            ['another', 0],
            [0, 0]
        ];
    }
}
