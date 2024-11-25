<?php

namespace Tests\Feature;

use App\Classes\SimilaresCombinator;
use App\Classes\StockManager;
use App\Helpers\Logger;
use App\Helpers\Sanitizer;
use App\Producto;
use App\Proveedor;
use App\Repositories\ProductosGranelRepository;
use App\Repositories\ProductosRepository;
use App\Repositories\SimilaresRepository;
use App\User;
use Database\Seeders\Productos\TestingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use stdClass;
use Tests\TestCase;

class ProductosRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestingSeeder::class);
    }


    /**
     * @dataProvider testCasesShowByProveedor
     */
    public function test_showByProveedor_when_called_return_correct_result(
        $productoId,
        $proveedorId,
        $expectedName,
        $expectedCode
    ) {
        Auth::shouldReceive('user')->andReturn(new User(['id' => 1]));
        
        $repo = new ProductosRepository(
            new Logger(),
            new StockManager(
                new Logger()
            ),
            new Sanitizer(),
            new ProductosGranelRepository(new Logger())
        );

        $actual = $repo->showByProveedor($productoId, $proveedorId);

        $this->assertEquals($expectedName, $actual?->nombre);
        $this->assertEquals($expectedCode, $actual?->pivot->codigo);
    }

    private function testCasesShowByProveedor(): array
    {
        return [
            [1, 1, 'Levic', 'Levis Code 1'],
            [1, 2, 'Quepharma', 'Quepharma Code'],
            [2, 1, null, null],
        ];
    }
}
