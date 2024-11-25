<?php

namespace Tests\Unit;

use App\Classes\ICierreAperturaCajaBuilder;
use App\Helpers\LoggerBuilder;
use App\Producto;
use App\Repositories\IAperturasCajaRepository;
use App\Repositories\IDescuentosRepository;
use App\Repositories\IProductosRepository;
use App\Repositories\VentasRepository;
use App\User;
use App\Venta;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery;
use stdClass;
use Tests\TestCase;

class VentasRepositoryTest extends TestCase
{
    private $_productosRepoMock;
    private $_descuentosRepoMock;
    private $_loggerMock;
    private $_aperturaCajaRepositoryMock;
    private $_ventaModelMock;
    private $_productoModelMock;
    private $_productosProveedoresCountStd;
    private $_productosProveedoresCantidadStd;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_productosRepoMock = Mockery::mock(IProductosRepository::class);
        /** @var \Mockery\MockInterface $_loggerMock */
        $this->_loggerMock = Mockery::mock(LoggerBuilder::class);
        $this->_loggerMock
            ->shouldReceive('success', 'error', 'link_id', 'user_id', 'log', 'module')
            ->andReturn($this->_loggerMock);

        $this->_aperturaCajaRepositoryMock = Mockery::mock(IAperturasCajaRepository::class);
        $this->_descuentosRepoMock = Mockery::mock(IDescuentosRepository::class);
        $this->_ventaModelMock = Mockery::mock(Venta::class);
        $this->_productoModelMock = Mockery::mock(Producto::class);

        $this->_ventaModelMock
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $this->_productoModelMock
            ->shouldReceive('getAttribute')
            ->with('id')
            ->andReturn(1);

        $this->_productosProveedoresCountStd = new stdClass;
        $this->_productosProveedoresCantidadStd = new stdClass;

        $this->_productosProveedoresCantidadStd->cantidad = fake()->numberBetween(2, 10);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @dataProvider testCases_delete
     */
    public function test_delete_when_happy_path($ventasProductos)
    {
        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('select')
            ->once()
            ->andReturn($ventasProductos);

        $this->_productosRepoMock
            ->shouldReceive('show')
            ->times(count($ventasProductos))
            ->andReturn(new Producto());

        $this->_productosRepoMock
            ->shouldReceive('modifyStockByAmount')
            ->times(count($ventasProductos));

        $this->_ventaModelMock
            ->shouldReceive('delete')
            ->once();

        DB::shouldReceive('commit')
            ->once();

        $this->_loggerMock
            ->shouldReceive('error')
            ->never();

        $this->_loggerMock
            ->shouldReceive('description')
            ->with(VentasRepository::class . '::delete finished')
            ->andReturn($this->_loggerMock);

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));

        $repo = new VentasRepository(
            $this->_aperturaCajaRepositoryMock,
            $this->_productosRepoMock,
            $this->_descuentosRepoMock,
            $this->_loggerMock
        );

        $actual = $repo->delete($this->_ventaModelMock);

        $this->assertEquals(true, $actual);
    }

    /**
     * @dataProvider testCases_delete
     */
    public function test_delete_when_exception($ventasProductos, $exceptionMessage)
    {
        $exceptionToBeThrown = new Exception($exceptionMessage);

        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('select')
            ->once()
            ->andReturn($ventasProductos);

        $this->_productosRepoMock
            ->shouldReceive('show')
            ->times(count($ventasProductos))
            ->andReturn(new Producto());

        $this->_productosRepoMock
            ->shouldReceive('modifyStockByAmount')
            ->times(count($ventasProductos));

        $this->_ventaModelMock
            ->shouldReceive('delete')
            ->andThrow($exceptionToBeThrown);

        DB::shouldReceive('commit')
            ->never();

        DB::shouldReceive('rollBack')
            ->once();

        $this->_loggerMock
            ->shouldReceive('description')
            ->with(VentasRepository::class . '::delete finished with error')
            ->andReturn($this->_loggerMock);

        $this->_loggerMock->shouldReceive('exception')
            ->with($exceptionToBeThrown)
            ->andReturn($this->_loggerMock)
            ->once();


        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]))
            ->once();

        $repo = new VentasRepository(
            $this->_aperturaCajaRepositoryMock,
            $this->_productosRepoMock,
            $this->_descuentosRepoMock,
            $this->_loggerMock
        );

        $actual = $repo->delete($this->_ventaModelMock);

        $this->assertEquals(false, $actual);
    }

    private function testCases_delete(): array
    {
        $prodVenta1 = new stdClass;
        $prodVenta1->producto_id = 1;
        $prodVenta1->cantidad = 2;

        $prodVenta2 = new stdClass;
        $prodVenta2->producto_id = 2;
        $prodVenta2->cantidad = 1;
        return [
            [
                'productos_venta' => [
                    $prodVenta1, $prodVenta2
                ],
                'exceptionMessage' => fake()->realText(50)
            ],
            [
                'productos_venta' => [
                    $prodVenta2
                ],
                'exceptionMessage' => fake()->realText(50)
            ],
            [
                'productos_venta' => [],
                'exceptionMessage' => fake()->realText(50)
            ]
        ];
    }

    public function test_deleteItem_when_happy_path()
    {
        $this->_productosProveedoresCountStd->valor = fake()->numberBetween(2, 10);

        $totalObj = new stdClass;
        $totalObj->total = 100;
        $profitObj = new stdClass;
        $profitObj->profit = 150;


        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('select')
            ->times(2)
            ->andReturn([$this->_productosProveedoresCountStd], [$this->_productosProveedoresCantidadStd]);

        $this->_ventaModelMock
            ->shouldReceive('delete')
            ->never();

        $this->_productosRepoMock
            ->shouldReceive('modifyStockByAmount')
            ->with($this->_productoModelMock, $this->_productosProveedoresCantidadStd->cantidad)
            ->times(1);

        DB::shouldReceive('table->where->where->delete')
            ->once();

        DB::shouldReceive('table->where->update')
            ->once();

        DB::shouldReceive('select')
            ->times(2)
            ->andReturn([$totalObj], [$profitObj]);

        DB::shouldReceive('commit')
            ->once();

        $this->_loggerMock
            ->shouldReceive('description')
            ->with(VentasRepository::class . '::deleteItem finished for venta ' . $this->_ventaModelMock->id)
            ->once()
            ->andReturn($this->_loggerMock);

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));

        $repo = new VentasRepository(
            $this->_aperturaCajaRepositoryMock,
            $this->_productosRepoMock,
            $this->_descuentosRepoMock,
            $this->_loggerMock
        );

        $actual = $repo->deleteItem($this->_ventaModelMock, $this->_productoModelMock);

        $this->assertEquals(true, $actual);
    }

    public function test_deleteItem_when_num_items_is_1()
    {
        $ventaProductoMock = new stdClass;
        $ventaProductoMock->producto_id = 1;
        $ventaProductoMock->cantidad = $this->_productosProveedoresCantidadStd->cantidad;

        $this->_productosProveedoresCountStd->valor = 1;

        DB::shouldReceive('beginTransaction')
            ->times(2);

        DB::shouldReceive('select')
            ->times(2)
            ->andReturn([$this->_productosProveedoresCountStd], [$ventaProductoMock]);

        $this->_ventaModelMock
            ->shouldReceive('delete')
            ->once();

        $this->_productosRepoMock
            ->shouldReceive('show')
            ->andReturn($this->_productoModelMock)
            ->once();

        $this->_productosRepoMock
            ->shouldReceive('modifyStockByAmount')
            ->with($this->_productoModelMock, $this->_productosProveedoresCantidadStd->cantidad)
            ->times(1);

        DB::shouldReceive('commit')
            ->times(2);

        DB::shouldReceive('table->where->where->delete')
            ->never();

        $this->_loggerMock
            ->shouldReceive('description')
            ->with(VentasRepository::class . '::delete finished')
            ->once()
            ->andReturn($this->_loggerMock);

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));

        $repo = new VentasRepository(
            $this->_aperturaCajaRepositoryMock,
            $this->_productosRepoMock,
            $this->_descuentosRepoMock,
            $this->_loggerMock
        );

        $actual = $repo->deleteItem($this->_ventaModelMock, $this->_productoModelMock);

        $this->assertEquals(true, $actual);
    }

    public function test_deleteItem_when_exception()
    {
        $this->_productosProveedoresCountStd->valor = 2;

        DB::shouldReceive('beginTransaction')
            ->times(1);

        DB::shouldReceive('select')
            ->times(2)
            ->andReturn([$this->_productosProveedoresCountStd], [$this->_productosProveedoresCantidadStd]);

        $this->_productosRepoMock
            ->shouldReceive('modifyStockByAmount')
            ->with($this->_productoModelMock, $this->_productosProveedoresCantidadStd->cantidad)
            ->times(1);

        DB::shouldReceive('table->where->where->delete')
            ->once()
            ->andThrow(new Exception());

        DB::shouldReceive('commit')
            ->never();

        DB::shouldReceive('rollBack')
            ->once();

        $this->_loggerMock
            ->shouldReceive('description')
            ->with(VentasRepository::class . '::deleteItem finished with error for venta ' . $this->_ventaModelMock->id)
            ->once()
            ->andReturn($this->_loggerMock);

        $this->_loggerMock
            ->shouldReceive('exception')
            ->once()
            ->andReturn($this->_loggerMock);

        Auth::shouldReceive('user')
            ->andReturn(new User(['id' => 1]));

        $repo = new VentasRepository(
            $this->_aperturaCajaRepositoryMock,
            $this->_productosRepoMock,
            $this->_descuentosRepoMock,
            $this->_loggerMock
        );

        $actual = $repo->deleteItem($this->_ventaModelMock, $this->_productoModelMock);

        $this->assertEquals(false, $actual);
    }
}
