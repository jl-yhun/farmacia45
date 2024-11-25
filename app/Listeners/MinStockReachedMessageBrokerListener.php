<?php

namespace App\Listeners;

use App\Classes\GranelCalculator;
use App\Classes\IStockManager;
use App\Classes\PurchaseCalculator;
use App\Events\MinStockReached;
use App\Helpers\LoggerBuilder;
use App\Repositories\IOrdenesCompraRepository;
use App\Repositories\IProductosRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MinStockReachedMessageBrokerListener
{
    private $_logger;
    private $_productoRepository;
    private $_stockManager;
    private $_ordenesCompraRepository;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        LoggerBuilder $logger,
        IProductosRepository $productosRepository,
        IStockManager $stockManager,
        IOrdenesCompraRepository $ordenesCompraRepository
    ) {
        $this->_logger = $logger;
        $this->_productoRepository = $productosRepository;
        $this->_stockManager = $stockManager;
        $this->_ordenesCompraRepository = $ordenesCompraRepository;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\MinStockReached  $event
     * @return void
     */
    public function handle(MinStockReached $event)
    {
        $producto = $this->_productoRepository->show($event->productoId);
        $proveedor = $this->_stockManager->getBestProviderForPurchase($producto);

        if ($proveedor == null) {
            // TODO: Agregar un proveedor Unknown y agregar a ese pedido
            $this->_logger
                ->info()
                ->description('Ningún proveedor configurado.')
                ->link_id($event->productoId)
                ->module($this::class)
                ->log();
            return;
        }

        try {
            $cantidad = PurchaseCalculator::calculateAmountForPurchase($producto);

            $itemToAdd = [
                'proveedor_id' => $proveedor['id'],
                'producto_id' => $event->productoId,
                'cantidad' => $cantidad
            ];

            $this->_ordenesCompraRepository->addItem($itemToAdd);

            $this->_logger
                ->info()
                ->description('MinStockReachedMessageBrokerListener finished.')
                ->after(json_encode($itemToAdd))
                ->link_id($event->productoId)
                ->module($this::class)
                ->log();
        } catch (\Throwable $th) {
            $this->_logger
                ->error()
                ->exception($th)
                ->description('MinStockReachedMessageBrokerListener error.')
                ->before(json_encode($itemToAdd))
                ->link_id($event->productoId)
                ->module($this::class)
                ->log();
        }
        // TODO: Pub/Sub Integration
    }
}
