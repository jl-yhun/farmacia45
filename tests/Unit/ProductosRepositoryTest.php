<?php

namespace Tests\Unit;

use App\Classes\IStockManager;
use App\Exceptions\StockLessThanZeroException;
use App\Helpers\LoggerBuilder;
use App\Helpers\SanitizerBuilder;
use App\Producto;
use App\Repositories\IProductosGranelRepository;
use App\Repositories\ProductosRepository;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\TestCase;

class ProductosRepositoryTest extends TestCase
{
    private $_loggerBuilderMock;
    private $_stockManagerMock;
    private $_productosGranelRepositoryMock;
    private $_sanitizerBuilderMock;
    private $_productoMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_loggerBuilderMock = $this->createMock(LoggerBuilder::class);
        $this->_stockManagerMock = $this->getMockBuilder(IStockManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->_productosGranelRepositoryMock = $this->createMock(IProductosGranelRepository::class);
        $this->_sanitizerBuilderMock = $this->createMock(SanitizerBuilder::class);
        $this->_productoMock = $this->getMockBuilder(Producto::class)
            ->onlyMethods(['save', 'toArray', 'update', 'isDirty'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function test_modifyStockByAmount_when_called_reduce_stock()
    {

        $this->_stockManagerMock
            ->expects($this->once())
            ->method('tryTriggerMinStockReachedEvent');

        $this->_productoMock
            ->method('save')
            ->willReturn(true);

        $this->_productoMock->stock = 55;
        $this->_productoMock->id = 1;

        $obj = new ProductosRepository(
            $this->_loggerBuilderMock,
            $this->_stockManagerMock,
            $this->_sanitizerBuilderMock,
            $this->_productosGranelRepositoryMock
        );
        $obj->modifyStockByAmount($this->_productoMock, -15);

        $this->assertEquals($this->_productoMock->stock, 40);
    }

    public function test_modifyStockByAmount_when_amount_exceed_current_stock_return_exception()
    {
        $this->_productoMock->stock = 1;
        $this->_productoMock->id = 1;

        $this->_stockManagerMock
            ->expects($this->never())
            ->method('tryTriggerMinStockReachedEvent');

        $this->expectException(StockLessThanZeroException::class);

        $obj = new ProductosRepository(
            $this->_loggerBuilderMock,
            $this->_stockManagerMock,
            $this->_sanitizerBuilderMock,
            $this->_productosGranelRepositoryMock
        );
        $obj->modifyStockByAmount($this->_productoMock, -2);
    }

    public function test_update_when_stock_updated_try_trigger_event_min_stock_reached()
    {
        // Arrange
        $sanitizerBuilderMock = $this->createMock(SanitizerBuilder::class);

        // Assert
        $this->_stockManagerMock
            ->expects($this->once())
            ->method('tryTriggerMinStockReachedEvent');

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));
        DB::shouldReceive('beginTransaction');
        DB::shouldReceive('rollBack');
        DB::shouldReceive('commit');

        // Act
        $obj = new ProductosRepository(
            $this->_loggerBuilderMock,
            $this->_stockManagerMock,
            $sanitizerBuilderMock,
            $this->_productosGranelRepositoryMock
        );

        $obj->update(['stock' => '4'], $this->_productoMock);
    }

    public function test_update_when_no_stock_updated_dont_try_trigger_event_min_stock_reached()
    {
        // Arrange
        $sanitizerBuilderMock = $this->createMock(SanitizerBuilder::class);

        // Assert
        $this->_stockManagerMock
            ->expects($this->never())
            ->method('tryTriggerMinStockReachedEvent');

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));
        DB::shouldReceive('beginTransaction');
        DB::shouldReceive('rollBack');
        DB::shouldReceive('commit');

        // Act
        $obj = new ProductosRepository(
            $this->_loggerBuilderMock,
            $this->_stockManagerMock,
            $sanitizerBuilderMock,
            $this->_productosGranelRepositoryMock
        );

        $obj->update(['cantidad' => '4', 'nombre' => 'Prod1'], $this->_productoMock);
    }
}
