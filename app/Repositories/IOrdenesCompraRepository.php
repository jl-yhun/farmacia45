<?php

namespace App\Repositories;

interface IOrdenesCompraRepository
{
    public function getOrdered();
    public function suggested();
    public function notAvailable();
    public function addItem($itemData);
    public function patchItem($ocId, $productId, $itemData);
    public function deleteItem($ocId, $productId);
    public function show($id);
    public function create($data);
    public function patch($ocId, $input);
    // public function update($data, $id);
    // public function delete($id);
}
