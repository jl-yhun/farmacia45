<?php

namespace App\Repositories;

use App\Classes\ProductosQuery;
use Symfony\Component\HttpFoundation\InputBag;

interface IProductosRepository
{
    public function get(InputBag $queryParams);
    public function all();
    public function getOrderedBy($property);
    public function modifyStockByAmount($id, $amount = -1);
    public function show($id);
    public function showByCode($codigo_barras);
    public function create($data);
    public function update($data, $id);
    public function delete($id);
    public function search($searchText);
    public function makePurchasePriceVisibleTemporally(&$productos);

    public function linkProveedor($id, $input);
    public function updateLinkedProveedor($id, $proveedor_id, $input);
    public function deleteLinkedProveedor($id, $proveedor_id);
    public function showByProveedor($id, $proveedor_id);

    public function setUpInventory($codigo_barras, $diff);
    public function removeStock($codigo_barras);
}
