<?php

namespace Tests\Unit;

use App\Classes\ProductosQuery\ProductosQuery;
use App\Classes\ProductosQuery\ProductosQueryBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;

class ProductosQueryTest extends TestCase
{
    /**
     * @dataProvider testCases_WhenCalled
     */
    public function test_whenCalled(array $params, string $expected)
    {
        $productosQuery = new ProductosQuery();

        foreach ($params as $key => $value) {
            $productosQuery->$key($value);
        }

        $actual = $productosQuery->finish();

        $newExpected = preg_replace("/[\n\r\t]/", "", $expected);
        $newActual = preg_replace("/[\n\r\t]/", "", $actual);

        $this->assertEquals($newExpected, $newActual);
    }

    /**
     * @dataProvider testCases_WhenCalled
     */
    public function test_building(array $params, string $expected)
    {

        $actualObj = ProductosQueryBuilder::buildFromQueryParams(new InputBag($params));

        $newExpected = preg_replace("/[\n\r\t]/", "", $expected);
        $newActual = preg_replace("/[\n\r\t]/", "", $actualObj->finish());

        $this->assertEquals($newExpected, $newActual);
    }

    private function testCases_whenCalled(): array
    {
        $baseQuery = <<<EOT
        SELECT DISTINCT p.id, p.codigo_barras, p.nombre, p.descripcion, p.categoria_id, p.caducidad, p.compra, p.venta, p.stock, 
        p.min_stock, p.max_stock, c.nombre categoria, 
        (SELECT COUNT(1) FROM productos_proveedores pp WHERE pp.producto_id = p.id) num_proveedores, 
        (SELECT COUNT(1) FROM productos_similares ps INNER JOIN productos psp ON psp.id = ps.similar_producto_id AND psp.deleted_at IS NULL WHERE ps.base_producto_id = p.id) num_similares, 
        IF(pg.id IS NOT NULL, 1, 0) isGranel, 
        IFNULL(pg.unidades_paquete, 0) unidades_paquete, 
        IFNULL((SELECT SUM(cantidad) FROM ordenes_compra_productos ocp WHERE ocp.producto_id = p.id), 0) pedidos, 
        (SELECT COUNT(1) FROM productos WHERE deleted_at IS NULL) total_productos 
        FROM productos p 
        LEFT JOIN tags_models tm ON tm.tageable_id = p.id 
        INNER JOIN categorias c ON c.id = p.categoria_id 
        LEFT JOIN productos_granel pg ON pg.producto_id = p.id 
        WHERE p.deleted_at IS NULL
        EOT;
        return [
            [
                [
                ],
                <<<EOT
                $baseQuery ORDER BY id DESC LIMIT 0,50
                EOT
            ],
            [
                [
                    "term" => "VIVRADOXI",
                    "tags" => ['revisar', 'para-revisar'],
                    'categoria_id' => 1,
                    'limit'=> [1, 20]
                ],
                <<<EOT
                $baseQuery 
                AND (p.nombre LIKE '%VIVRADOXI%' OR p.descripcion LIKE '%VIVRADOXI%' OR p.codigo_barras LIKE '%VIVRADOXI%') 
                AND tm.tag_id IN (SELECT id FROM tags WHERE nombre IN('revisar', 'para-revisar')) 
                AND p.categoria_id=1 
                ORDER BY id DESC LIMIT 1,20
                EOT
            ],
            [
                [
                    "term" => "TERM",
                    "compra" => [10, 50],
                    "venta" => [20, 100],
                    "tags" => ["agotado"],
                    "stock" => 0,
                    "caducidad" => "2022-10-10"
                ],
                <<<EOT
                $baseQuery 
                AND (p.nombre LIKE '%TERM%' OR p.descripcion LIKE '%TERM%' OR p.codigo_barras LIKE '%TERM%') 
                AND p.compra BETWEEN 10 AND 50 
                AND p.venta BETWEEN 20 AND 100 
                AND tm.tag_id IN (SELECT id FROM tags WHERE nombre IN('agotado')) 
                AND p.stock=0 
                AND p.caducidad='2022-10-10' 
                ORDER BY id DESC LIMIT 0,50
                EOT
            ]
        ];
    }
}
