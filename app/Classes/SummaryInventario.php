<?php

namespace App\Classes;

use App\Enums\TipoInventario;
use App\Enums\TipoResumenInventario;
use App\Helpers\LoggerBuilder;
use App\Repositories\IProductosRepository;

class SummaryInventario implements ISummaryInventario
{
    private $_summaryArray = [];
    private $_productosRepository;
    private $_logger;

    public function __construct(IProductosRepository $productosRepository, LoggerBuilder $logger)
    {
        $this->_productosRepository = $productosRepository;
        $this->_logger = $logger;
    }

    public function calculateDiff($inInventory, $inDb): array
    {

        foreach ($inDb as $productInDb) {
            $inInventoryItem = $inInventory[$productInDb['id']] ?? null;

            if (!isset($inInventoryItem) && $productInDb['stock'] == 0)
                continue;
            else if (
                !isset($inInventoryItem) &&
                $productInDb['stock'] > 0
            ) { // Missings in physic Inventory
                $this->addInexistencia(
                    producto_id: $productInDb['id'],
                    data: [
                        'codigo_barras' => $productInDb['codigo_barras'],
                        'nombre' => $productInDb['nombre'],
                        'descripcion' => $productInDb['descripcion'],
                        'stock' => $productInDb['stock'],
                        'compra' => $productInDb['compra'],
                        'venta' => $productInDb['venta']
                    ]
                );
            } else if ($this->isThereExcedent($inInventoryItem, $productInDb)) {
                $this->addSobrante(
                    producto_id: $productInDb['id'],
                    data: $this->calculateExcedentOrMissing($inInventoryItem, $productInDb)
                );
            } else if ($this->isThereMissing($inInventoryItem, $productInDb)) {
                $this->addFaltante(
                    producto_id: $productInDb['id'],
                    data: $this->calculateExcedentOrMissing($inInventoryItem, $productInDb)
                );
            } else {
                // $this->addCorrecto(
                //     producto_id: $productInDb['id'],
                //     data: [
                //         'codigo_barras' => $productInDb['codigo_barras'],
                //         'nombre' => $productInDb['nombre'],
                //         'descripcion' => $productInDb['descripcion']
                //     ]
                // );
            }
            $inInventory[$productInDb['id']]['estado'] = 'ok';
        }

        // // Missings in logic Inventory
        // foreach ($inInventory as $key => $item) {
        //     if (isset($item['estado']) && $item['estado'] == 'ok') continue;

        //     $this->addInexistencia(
        //         codigo: $key,
        //         data: [
        //             'codigo_barras' => $key,
        //             'nombre' => $productInDb->nombre,
        //             'descripcion' => $productInDb->descripcion,
        //             'stock' => $item['cantidad'],
        //             'compra' => $item['compra'],
        //             'venta' => $item['venta'],
        //             'donde' => TipoInventario::Logico->value
        //         ]
        //     );
        // }

        return $this->_summaryArray;
    }

    public function finish($diff)
    {
        foreach ($diff['diferencias'] as $producto_id => $item) {

            $this->_logger
                ->success()
                ->module($this::class . "::finish")
                ->user_id(auth()->user()->id)
                ->after(json_encode($item))
                ->link_id($producto_id)
                ->description('aplicando diferencia - start')
                ->log();

            $this->_productosRepository->setUpInventory($producto_id, $item);

            $this->_logger
                ->success()
                ->module($this::class . "::finish")
                ->user_id(auth()->user()->id)
                ->after(json_encode($item))
                ->link_id($producto_id)
                ->description('aplicando diferencia - end')
                ->log();
        }

        if (isset($diff['inexistencias'])) {
            foreach ($diff['inexistencias'] as $producto_id => $item) {
                $this->_logger
                    ->success()
                    ->module($this::class . "::finish")
                    ->user_id(auth()->user()->id)
                    ->after(json_encode($item))
                    ->link_id($producto_id)
                    ->description('aplicando inexistencia - start')
                    ->log();

                $this->_productosRepository->removeStock($item['codigo_barras']);

                $this->_logger
                    ->success()
                    ->module($this::class . "::finish")
                    ->user_id(auth()->user()->id)
                    ->after(json_encode($item))
                    ->link_id($producto_id)
                    ->description('aplicando inexistencia - end')
                    ->log();
            }
        }
    }


    private function addDiferencias($producto_id): ISummaryInventario
    {
        if (!isset($this->_summaryArray['diferencias'][$producto_id]))
            $this->_summaryArray['diferencias'][$producto_id] = [
                'codigo_barras' => '',
                'nombre' => '',
                'descripcion' => '',
                'stock' => 0,
                'compra' => 0,
                'venta' => 0
            ];
        return $this;
    }

    private function addInexistencias($producto_id): ISummaryInventario
    {
        if (!isset($this->_summaryArray['inexistencias'][$producto_id]))
            $this->_summaryArray['inexistencias'][$producto_id] = [
                'codigo_barras' => '',
                'nombre' => '',
                'descripcion' => '',
                'stock' => 0,
                'compra' => 0,
                'venta' => 0
            ];
        return $this;
    }

    private function addSobrante($producto_id, array $data): ISummaryInventario
    {
        $this->addDiferencias($producto_id);
        $this->add(TipoResumenInventario::Sobrante, $producto_id, $data);
        return $this;
    }

    private function addFaltante($producto_id, array $data): ISummaryInventario
    {
        $this->addDiferencias($producto_id);
        $this->add(TipoResumenInventario::Faltante, $producto_id, $data);
        return $this;
    }

    private function addCorrecto($producto_id, array $data): ISummaryInventario
    {
        $this->addDiferencias($producto_id);
        $this->add(TipoResumenInventario::Concidencia, $producto_id, [
            'stock' => 0,
            'venta' => 0,
            'compra' => 0,
            ...$data
        ]);
        return $this;
    }

    private function addInexistencia($producto_id, array $data): ISummaryInventario
    {
        $this->addInexistencias($producto_id);
        $this->add(TipoResumenInventario::Inexistencia, $producto_id, $data);
        return $this;
    }

    private function add(
        TipoResumenInventario $tipo,
        $producto_id,
        array $data
    ) {
        $actualKey = $tipo == TipoResumenInventario::Inexistencia ? 'inexistencias' : 'diferencias';
        if (count($data) > 0)
            $this->_summaryArray[$actualKey][$producto_id] = $data;
    }

    private function isThereExcedent($inventoryItem, $inDbItem): bool
    {
        return $inventoryItem['cantidad'] > $inDbItem['stock'] ||
            $inventoryItem['compra'] > $inDbItem['compra'] ||
            $inventoryItem['venta'] > $inDbItem['venta'];
    }

    private function isThereMissing($inventoryItem, $inDbItem): bool
    {
        return $inventoryItem['cantidad'] < $inDbItem['stock'] ||
            $inventoryItem['compra'] < $inDbItem['compra'] ||
            $inventoryItem['venta'] < $inDbItem['venta'];
    }

    private function calculateSymbol($diff)
    {
        return ($diff > 0 ? '+' : '') . $diff;
    }

    private function calculateExcedentOrMissing($inInventoryItem, $productInDb): array
    {
        $diffStock = $inInventoryItem['cantidad'] - $productInDb['stock'];
        $diffCompra = $inInventoryItem['compra'] - $productInDb['compra'];
        $diffVenta = $inInventoryItem['venta'] - $productInDb['venta'];

        return [
            'codigo_barras' =>  $productInDb['codigo_barras'],
            'nombre' => $productInDb['nombre'],
            'descripcion' => $productInDb['descripcion'],
            'current_stock' => $productInDb['stock'],
            'stock' => $this->calculateSymbol($diffStock),
            'compra' => $this->calculateSymbol($diffCompra),
            'venta' => $this->calculateSymbol($diffVenta)
        ];
    }
}
