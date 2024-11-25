<?php

namespace App\Classes\ProductosQuery;

class ProductosQuery implements IProductosQuery
{
    public $take;
    public $skip;

    private $query;
    private $baseQuery;

    public function __construct()
    {
        $this->baseQuery = <<<EOT
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
    }

    private function tryAddLimitClause(): string
    {
        if ($this->take != null)
            return "LIMIT {$this->skip},{$this->take}";
        else
            return "LIMIT 0,50";
    }

    public function finish(): string
    {
        $limitQuery = $this->tryAddLimitClause();
        $orderBy = " ORDER BY id DESC $limitQuery";

        return $this->baseQuery . $this->query . $orderBy;
    }

    public function term(string $term): IProductosQuery
    {
        $this->query .= " AND (p.nombre LIKE '%$term%' OR p.descripcion LIKE '%$term%' OR p.codigo_barras LIKE '%$term%')";
        return $this;
    }

    public function categoria_id(string $categoria_id): IProductosQuery
    {
        $this->query .= " AND p.categoria_id=$categoria_id";
        return $this;
    }

    public function caducidad(string $caducidad): IProductosQuery
    {
        $this->query .= " AND p.caducidad='$caducidad'";
        return $this;
    }

    public function compra(array $compra): IProductosQuery
    {
        $this->query .= " AND p.compra BETWEEN $compra[0] AND $compra[1]";
        return $this;
    }

    public function venta(array $venta): IProductosQuery
    {
        $this->query .= " AND p.venta BETWEEN $venta[0] AND $venta[1]";
        return $this;
    }

    public function stock(int $stock): IProductosQuery
    {
        $this->query .= " AND p.stock=$stock";
        return $this;
    }

    public function tags(array $tags): IProductosQuery
    {
        $this->query .= " AND tm.tag_id IN (SELECT id FROM tags WHERE nombre IN('" . implode("', '", $tags) . "'))";
        return $this;
    }

    public function limit(array $limit): IProductosQuery
    {
        $this->skip = $limit[0];
        $this->take = $limit[1];
        return $this;
    }
}
