<?php

namespace App\Providers;

use App\Classes\CierreAperturaCaja;
use App\Classes\CierreCaja\Contracts\IGastosCalculator;
use App\Classes\CierreCaja\Contracts\IServiciosRecargasCalculator;
use App\Classes\CierreCaja\Contracts\ITransferenciasCalculator;
use App\Classes\CierreCaja\Contracts\IVentasCalculator;
use App\Classes\CierreCaja\Implementations\GastosCalculator;
use App\Classes\CierreCaja\Implementations\ServiciosRecargasCalculator;
use App\Classes\CierreCaja\Implementations\TransferenciasCalculator;
use App\Classes\CierreCaja\Implementations\VentasCalculator;
use App\Classes\ICierreAperturaCajaBuilder;
use App\Classes\ISimilaresCombinator;
use App\Classes\IStockManager;
use App\Classes\ISummaryInventario;
use App\Classes\SimilaresCombinator;
use App\Classes\StockManager;
use App\Classes\SummaryInventario;
use App\Helpers\ILogger;
use App\Helpers\Logger;
use App\Helpers\LoggerBuilder;
use App\Helpers\Sanitizer;
use App\Helpers\SanitizerBuilder;
use App\OrdenCompra;
use App\Repositories\ApartadosRepository;
use App\Repositories\AperturasCajaRepository;
use App\Repositories\CategoriasRepository;
use App\Repositories\ConfiguracionRepository;
use App\Repositories\DescuentosRepository;
use App\Repositories\GastosRepository;
use App\Repositories\IApartadosRepository;
use App\Repositories\IAperturasCajaRepository;
use App\Repositories\ICategoriasRepository;
use App\Repositories\IConfiguracionRepository;
use App\Repositories\IDescuentosRepository;
use App\Repositories\IGastosRepository;
use App\Repositories\IPagoServiciosRepository;
use App\Repositories\IInventarioRepository;
use App\Repositories\InventarioRepository;
use App\Repositories\IOrdenesCompraRepository;
use App\Repositories\IPerdidasRepository;
use App\Repositories\IPermisosRepository;
use App\Repositories\IProductosGranelRepository;
use App\Repositories\IProductosRepository;
use App\Repositories\IProveedoresRepository;
use App\Repositories\IPuntoVentaRepository;
use App\Repositories\IRolesRepository;
use App\Repositories\ISimilaresRepository;
use App\Repositories\ITagRepository;
use App\Repositories\ITransferenciasRepository;
use App\Repositories\IUsuariosRepository;
use App\Repositories\IVentasRepository;
use App\Repositories\OrdenesCompraRepository;
use App\Repositories\PagoServiciosRepository;
use App\Repositories\PerdidasRepository;
use App\Repositories\PermisosRepository;
use App\Repositories\ProductosGranelRepository;
use App\Repositories\ProductosRepository;
use App\Repositories\ProveedoresRepository;
use App\Repositories\PuntoVentaRepository;
use App\Repositories\RolesRepository;
use App\Repositories\SimilaresRepository;
use App\Repositories\TagRepository;
use App\Repositories\TransferenciasRepository;
use App\Repositories\UsuariosRepository;
use App\Repositories\VentasRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

use function Psy\bin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ICategoriasRepository::class, CategoriasRepository::class);
        $this->app->bind(IGastosRepository::class, GastosRepository::class);
        $this->app->bind(IPerdidasRepository::class, PerdidasRepository::class);
        $this->app->bind(IProductosRepository::class, ProductosRepository::class);
        $this->app->bind(IUsuariosRepository::class, UsuariosRepository::class);
        $this->app->bind(IPermisosRepository::class, PermisosRepository::class);
        $this->app->bind(IPuntoVentaRepository::class, PuntoVentaRepository::class);
        $this->app->bind(IConfiguracionRepository::class, ConfiguracionRepository::class);
        $this->app->bind(IVentasRepository::class, VentasRepository::class);
        $this->app->bind(IDescuentosRepository::class, DescuentosRepository::class);
        $this->app->bind(IPagoServiciosRepository::class, PagoServiciosRepository::class);
        $this->app->bind(ITransferenciasRepository::class, TransferenciasRepository::class);
        $this->app->bind(IInventarioRepository::class, InventarioRepository::class);
        $this->app->bind(IAperturasCajaRepository::class, AperturasCajaRepository::class);
        $this->app->bind(IOrdenesCompraRepository::class, OrdenesCompraRepository::class);
        $this->app->bind(IApartadosRepository::class, ApartadosRepository::class);
        $this->app->bind(ISimilaresRepository::class, SimilaresRepository::class);
        $this->app->bind(IRolesRepository::class, RolesRepository::class);
        $this->app->bind(IProveedoresRepository::class, ProveedoresRepository::class);
        $this->app->bind(IProductosGranelRepository::class, ProductosGranelRepository::class);
        $this->app->bind(ITagRepository::class, TagRepository::class);
        $this->app->bind(IVentasCalculator::class, VentasCalculator::class);
        $this->app->bind(IGastosCalculator::class, GastosCalculator::class);
        $this->app->bind(IServiciosRecargasCalculator::class, ServiciosRecargasCalculator::class);
        $this->app->bind(ITransferenciasCalculator::class, TransferenciasCalculator::class);


        $this->app->bind(LoggerBuilder::class, Logger::class);
        $this->app->bind(ICierreAperturaCajaBuilder::class, CierreAperturaCaja::class);
        $this->app->bind(ISummaryInventario::class, SummaryInventario::class);
        $this->app->bind(ISimilaresCombinator::class, SimilaresCombinator::class);
        $this->app->bind(IStockManager::class, StockManager::class);
        $this->app->bind(SanitizerBuilder::class, Sanitizer::class);
        $this->app->bind(OrdenCompra::class, OrdenCompra::class);

        $this->loadHelpers();
    }
    protected function loadHelpers()
    {
        foreach (glob(__DIR__ . '/../Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }
}
