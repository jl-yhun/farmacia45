<?php

namespace Tests\Unit;

use App\Classes\StockManager;
use App\Enums\OrdenCompraEstado;
use App\Events\MinStockReached;
use App\Helpers\LoggerBuilder;
use App\OrdenCompra;
use App\Producto;
use App\Proveedor;
use App\Repositories\IProductosRepository;
use App\Repositories\ISimilaresRepository;
use App\User;
use Database\Seeders\ProductosSeed;
use Exception;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\TestCase;

class StockManagerTest extends TestCase
{
    private $_similaresRepoMock;
    private $_ordenCompraMockBuilder;
    private $_productoMock;
    private $_productosRepoMock;
    private $_loggerBuilderMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_similaresRepoMock = $this->createMock(ISimilaresRepository::class);

        $this->_ordenCompraMockBuilder = $this->getMockBuilder(OrdenCompra::class)
            ->onlyMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->_productoMock = $this->getMockBuilder(Producto::class)
            ->onlyMethods(['getPedidosAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->_productosRepoMock = $this->createMock(IProductosRepository::class);
        $this->_loggerBuilderMock = $this->createMock(LoggerBuilder::class);
    }

    /**
     * @dataProvider testCases_wasMinStockExcedeed
     */
    public function test_wasMinStockExcedeed_when_called_return_correct_result(
        $minStock,
        $currentStock,
        $expected
    ) {
        // Arrange
        $this->_productoMock->id = 30;
        $this->_productoMock->min_stock = $minStock;
        $this->_productoMock->stock = $currentStock;
        $this->_productoMock->pedidos = 0;

        $obj = new StockManager($this->_loggerBuilderMock);

        //Act
        $actual = $obj->wasMinStockExcedeed($this->_productoMock);

        // Assert
        $this->assertEquals($expected, $actual);
    }

    private static function testCases_wasMinStockExcedeed(): array
    {
        return [
            [5, 4, true],
            [4, 4, false],
            [3, 4, false]
        ];
    }

    public function test_tryAdjustStockFromOrderProducts_when_called_modify_stock_executed()
    {
        $product1Mock = Mockery::mock(Producto::class);
        $product2Mock = Mockery::mock(Producto::class);
        // Arrange
        $productosToReturn = [
            $product1Mock,
            $product2Mock
        ];

        $product1Mock->shouldReceive('getAttribute')
            ->with('pivot')
            ->andReturn($product1Mock);

        $product2Mock->shouldReceive('getAttribute')
            ->with('pivot')
            ->andReturn($product2Mock);

        $product1Mock->shouldReceive('getAttribute')
            ->with('cantidad')
            ->andReturn(1);

        $product2Mock->shouldReceive('getAttribute')
            ->with('cantidad')
            ->andReturn(2);

        $product1Mock->shouldReceive('getAttribute')
            ->with('isGranel')
            ->andReturn(false);

        $product2Mock->shouldReceive('getAttribute')
            ->with('isGranel')
            ->andReturn(false);

        $this->_ordenCompraMockBuilder->productos = $productosToReturn;
        $this->_ordenCompraMockBuilder->estado = OrdenCompraEstado::Aplicado->value;

        // Assert
        $this->_productosRepoMock
            ->expects($this->exactly(count($productosToReturn)))
            ->method('modifyStockByAmount');

        // Act
        $obj = new StockManager($this->_loggerBuilderMock);
        $obj->tryAdjustStockFromOrderProducts(
            $this->_ordenCompraMockBuilder,
            $this->_productosRepoMock
        );
    }

    public function test_tryAdjustStockFromOrderProducts_when_estado_not_aplicado_modify_stock_not_executed()
    {
        // Arrange
        $this->_ordenCompraMockBuilder->estado = OrdenCompraEstado::Pendiente->value;

        // Assert
        $this->_productosRepoMock
            ->expects($this->never())
            ->method('modifyStockByAmount');

        // Act
        $obj = new StockManager($this->_loggerBuilderMock);
        $obj->tryAdjustStockFromOrderProducts(
            $this->_ordenCompraMockBuilder,
            $this->_productosRepoMock
        );
    }

    /**
     * @dataProvider testCases_tryAdjustStockFromOrderProducts
     */
    public function test_tryAdjustStockFromOrderProducts_when_granel_apply_correctly(
        $isGranel,
        $unidadesPaquete,
        $cantidadPedida,
        $expected
    ) {
        $product1Mock = Mockery::mock(Producto::class);

        $product1Mock->shouldReceive('getAttribute')
            ->with('isGranel')
            ->andReturn($isGranel);

        $product1Mock->shouldReceive('getAttribute')
            ->with('unidades_paquete')
            ->andReturn($unidadesPaquete);

        $product1Mock
            ->shouldReceive('getAttribute')
            ->with('pivot')
            ->andReturn($product1Mock);

        $product1Mock
            ->shouldReceive('getAttribute')
            ->with('cantidad')
            ->andReturn($cantidadPedida);

        // Arrange
        $productosToReturn = [
            $product1Mock
        ];

        $this->_ordenCompraMockBuilder->productos = $productosToReturn;
        $this->_ordenCompraMockBuilder->estado = OrdenCompraEstado::Aplicado->value;

        // Assert
        $this->_productosRepoMock
            ->expects($this->once())
            ->method('modifyStockByAmount')
            ->with($productosToReturn[0], $expected);

        // Act
        $obj = new StockManager($this->_loggerBuilderMock);
        $obj->tryAdjustStockFromOrderProducts(
            $this->_ordenCompraMockBuilder,
            $this->_productosRepoMock
        );
    }

    private function testCases_tryAdjustStockFromOrderProducts(): array
    {
        // isGranel, unidades_paquete, cantidad, expected
        return [
            [true, 4, 2, 8],
            [false, 0, 5, 5],
            [true, 5, 3, 15],
            [false, 5, 3, 3]
        ];
    }

    /**
     * @dataProvider testCases_tryTriggerMinStockReachedEvent
     */
    public function test_tryTriggerMinStockReachedEvent($minStock, $stock, $pedidos, $trigger)
    {
        Event::fake();

        $this->_productoMock->id = 30;
        $this->_productoMock->min_stock = $minStock;
        $this->_productoMock->stock = $stock;

        $this->_productoMock
            ->method('getPedidosAttribute')
            ->willReturn($pedidos);

        $obj = new StockManager($this->_loggerBuilderMock);

        $obj->tryTriggerMinStockReachedEvent($this->_productoMock);

        if ($trigger)
            Event::assertDispatched(MinStockReached::class);
        else
            Event::assertNotDispatched(MinStockReached::class);
    }

    private function testCases_tryTriggerMinStockReachedEvent(): array
    {
        // [min_stock, stock, pedidos, trigger]
        return [
            [2, 1, 0, true],
            [3, 1, 2, false],
            [2, 0, 1, true],
            [3, 0, 5, false]
        ];
    }

    /**
     * @dataProvider testCases_getBestProviderForPurchase
     */
    public function test_getBestProviderForPurchase_when_called_return_correct_provider($return, $providerExpected)
    {
        $proveedoresMock = $this->getMockBuilder(Proveedor::class)
            ->onlyMethods(['toArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $proveedoresMock->method('toArray')->willReturn($return);

        $this->_productoMock->proveedores = $proveedoresMock;

        // Act
        $obj = new StockManager($this->_loggerBuilderMock);
        $actual = $obj->getBestProviderForPurchase($this->_productoMock);

        // Assert
        $this->assertEquals($providerExpected, $actual['nombre'] ?? null);
    }

    private static function testCases_getBestProviderForPurchase(): array
    {
        return [
            [
                [
                    [
                        "id" => 1,
                        "nombre" => "Quepharma",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 1,
                            "precio" => 8
                        ]
                    ],
                    [
                        "id" => 2,
                        "nombre" => "Levic",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => 7
                        ]
                    ],
                    [
                        "id" => 3,
                        "nombre" => "Depot",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 1,
                            "precio" => 5
                        ]
                    ]
                ],
                "Depot"
            ],
            [
                [
                    [
                        "id" => 1,
                        "nombre" => "Quepharma",
                        "pivot" => [
                            "disponible" => 0,
                            "default" => 1,
                            "precio" => 2
                        ]
                    ],
                    [
                        "id" => 2,
                        "nombre" => "Levic",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => 10
                        ]
                    ],
                    [
                        "id" => 3,
                        "nombre" => "Depot",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => 11
                        ]
                    ]
                ],
                "Levic"
            ],
            [
                [
                    [
                        "id" => 1,
                        "nombre" => "Quepharma",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => "2.5"
                        ]
                    ],
                    [
                        "id" => 2,
                        "nombre" => "Levic",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => "2.55"
                        ]
                    ],
                    [
                        "id" => 3,
                        "nombre" => "Depot",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => 2.6
                        ]
                    ]
                ],
                "Quepharma"
            ],
            [
                [
                    [
                        "id" => 1,
                        "nombre" => "Levic",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => "13.02"
                        ]
                    ],
                    [
                        "id" => 2,
                        "nombre" => "Quepharma",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 1,
                            "precio" => "13.42"
                        ]
                    ],
                    [
                        "id" => 3,
                        "nombre" => "Nadro",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => 15.53
                        ]
                    ],
                    [
                        "id" => 4,
                        "nombre" => "Equilibrio",
                        "pivot" => [
                            "disponible" => 1,
                            "default" => 0,
                            "precio" => 12.40
                        ]
                    ],

                ],
                "Quepharma"
            ],
            [
                [
                    [
                        "id" => 1,
                        "nombre" => "Quepharma",
                        "pivot" => [
                            "disponible" => 0,
                            "default" => 1,
                            "precio" => 10
                        ]
                    ]
                ],
                null
            ],
            [
                [],
                null
            ]
        ];
    }

    public function test_getBestProviderForPurchase_when_exception_return_null()
    {
        $proveedoresMock = $this->getMockBuilder(Proveedor::class)
            ->onlyMethods(['toArray'])
            ->disableOriginalConstructor()
            ->getMock();

        $proveedoresMock
            ->method('toArray')
            ->willThrowException(new Exception());

        $this->_productoMock->proveedores = $proveedoresMock;

        // Act
        $obj = new StockManager($this->_loggerBuilderMock);
        $actual = $obj->getBestProviderForPurchase($this->_productoMock);

        // Assert
        $this->assertNull($actual);
    }
}
