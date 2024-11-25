<?php

namespace Tests\Unit;

use App\Classes\ICierreAperturaCajaBuilder;
use App\Classes\IStockManager;
use App\Helpers\LoggerBuilder;
use App\OrdenCompra;
use App\Producto;
use App\Repositories\IAperturasCajaRepository;
use App\Repositories\IProductosRepository;
use App\Repositories\OrdenesCompraRepository;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;

class OrdenesCompraRepositoryTest extends TestCase
{
    private $_loggerBuilderMock;
    private $_productoRepositoryMock;
    private $_aperturaCajaRepositoryMock;
    private $_stockManagerMock;
    private $_ordenCompraModelMock;
    private $_productoModelMock;

    protected function setUp(): void
    {
        $this->_loggerBuilderMock = Mockery::mock(LoggerBuilder::class, []);
        $this->_stockManagerMock = $this->getMockBuilder(IStockManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->_aperturaCajaRepositoryMock = $this->createMock(IAperturasCajaRepository::class);
        $this->_productoRepositoryMock =  Mockery::mock(IProductosRepository::class);
        $this->_ordenCompraModelMock = Mockery::mock(OrdenCompra::class);
        $this->_productoModelMock = Mockery::mock(Producto::class);

        $this->_loggerBuilderMock
            ->shouldReceive([
                'success' => $this->_loggerBuilderMock,
                'user_id' => $this->_loggerBuilderMock,
                'module' => $this->_loggerBuilderMock,
                'method' => $this->_loggerBuilderMock,
                'link_id' => $this->_loggerBuilderMock,
                'after' => $this->_loggerBuilderMock,
                'log' => true
            ]);
    }

    public function test_addItem_when_item_exists_patch_amount()
    {
        $this->_ordenCompraModelMock
            ->shouldReceive([
                'where->where->orderBy->first' => $this->_ordenCompraModelMock,
                'find' => $this->_ordenCompraModelMock,
                'productos->where->first' => $this->_productoModelMock
            ]);

        $this->_productoModelMock
            ->shouldReceive('getAttribute')
            ->with('pivot')
            ->andReturn($this->_productoModelMock);

        $this->_productoModelMock
            ->shouldReceive('getAttribute')
            ->with('cantidad')
            ->andReturn(1);

        $this->_productoModelMock
            ->shouldReceive('setAttribute')
            ->with('cantidad', 3);

        $this->_productoModelMock
            ->shouldReceive('save')
            ->once();

        $this->_ordenCompraModelMock
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('commit')
            ->once();

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));

        $this->_loggerBuilderMock
            ->shouldReceive('description')
            ->with('tryIncreaseAmountIfExist finishes')
            ->once()
            ->andReturn($this->_loggerBuilderMock);

        $obj = new OrdenesCompraRepository(
            $this->_aperturaCajaRepositoryMock,
            $this->_loggerBuilderMock,
            $this->_productoRepositoryMock,
            $this->_stockManagerMock,
            $this->_ordenCompraModelMock
        );

        $obj->addItem([
            'producto_id' => 1,
            'proveedor_id' => 1,
            'cantidad' => 2
        ]);
    }

    public function test_addItem_when_item_does_not_exists_add_item()
    {

        $this->_ordenCompraModelMock
            ->shouldReceive([
                'where->where->orderBy->first' => $this->_ordenCompraModelMock,
                'find' => $this->_ordenCompraModelMock,
                'productos' => $this->_ordenCompraModelMock
            ]);

        $this->_ordenCompraModelMock
            ->shouldReceive('where->first')
            ->once()
            ->andReturn(null);

        $this->_ordenCompraModelMock
            ->shouldReceive('getAttribute')
            ->with('id')
            ->once()
            ->andReturn(1);

        $this->_ordenCompraModelMock
            ->shouldReceive('syncWithoutDetaching')
            ->once();

        $this->_productoRepositoryMock
            ->shouldReceive('showByProveedor')
            ->once()
            ->andReturnNull();

        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('commit')
            ->once();

        Auth::shouldReceive('user')
            ->once()
            ->andReturn(new User(['id' => 1]));

        $this->_loggerBuilderMock
            ->shouldReceive('description')
            ->with('Ok al agregar item a la orden de compra.')
            ->once()
            ->andReturn($this->_loggerBuilderMock);

        $obj = new OrdenesCompraRepository(
            $this->_aperturaCajaRepositoryMock,
            $this->_loggerBuilderMock,
            $this->_productoRepositoryMock,
            $this->_stockManagerMock,
            $this->_ordenCompraModelMock
        );

        $obj->addItem([
            'producto_id' => 1,
            'proveedor_id' => 1,
            'cantidad' => 2
        ]);
    }

    // TODO: Add other unit testing and replace Feature Testing

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
