<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Exports\VentasExport;
use App\Exports\VentasXProductoExport;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\GarantiasController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\HerramientasController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\MotivosGarantiaController;
use App\Http\Controllers\PerdidasController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\PuntoVentaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\VentasController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Auth::routes();
Route::resource('usuarios', UsuariosController::class);
Route::resource('reparaciones', 'ReparacionesController')->except('show');
Route::resource('garantias', GarantiasController::class);
Route::resource('gastos', GastosController::class)->except(['store']);
Route::resource('perdidas', PerdidasController::class);
Route::resource('motivos-garantias', MotivosGarantiaController::class);
Route::resource('categorias', CategoriasController::class);

Route::get('/', function () {
    return redirect()->route('punto-venta');
});

Route::view('movimientos/create', 'movimientos.create')->name('movimientos.create');
Route::view('apartados', 'apartados.index')->name('apartados.index');

Route::prefix('caja')->group(function () {
    Route::get('/', [PuntoVentaController::class, 'index'])->name('punto-venta');
    Route::get('/opening', [PuntoVentaController::class, 'opening'])->name('punto-venta.opening');
    Route::post('/open', [PuntoVentaController::class, 'open'])->name('punto-venta.open');
    Route::get('/close', [PuntoVentaController::class, 'close'])->name('punto-venta.close');
    Route::get('/reprint-last', [PuntoVentaController::class, 'reprintLast'])->name('punto-venta.reprint-last');
    Route::get('/cobro', [PuntoVentaController::class, 'cobro'])->name('cobro');
    Route::get('/descuento/{id?}', [PuntoVentaController::class, 'descuento'])->name('punto-venta.descuento');
});

Route::prefix('productos')->group(function () {
    Route::view('', 'productos.index')->name('productos.index')->can('productos.view');

    Route::patch('/{id?}', [ProductosController::class, 'update'])->name('productos.update');
    Route::get('/datatable', [ProductosController::class, 'datatable'])->name('productos.datatable');
    Route::get('/imprimir-etiqueta/{id}', [ProductosController::class, 'imprimirEtiqueta'])->name('productos.imprimir-etiqueta');
    Route::post('/imprimir-etiqueta', [ProductosController::class, 'doImprimirEtiqueta'])->name('productos.do-imprimir-etiqueta');
    Route::post('/buscar', [ProductosController::class, 'buscar'])->name('productos.buscar');
});

Route::view('similares/create/{base_id?}', 'similares.create')->name('similares.create');

Route::get('perdidas/producto/{id?}', [PerdidasController::class, 'productos'])->name('perdidas.productos');

Route::prefix('herramientas')->group(function () {
    Route::get('/conteo', [HerramientasController::class, 'conteo'])->name('herramientas.conteo');
});

Route::prefix('usuarios')->group(function () {
    Route::get('/cambiar/perfil', 'UsuariosController@cambiarPerfil')->name('usuarios.cambiar-perfil');
    Route::get('/cambiar/perfil/{id?}', 'UsuariosController@cambiarPerfilPassword')->name('usuarios.cambiar-perfil-password');
    Route::post('/cambiar/perfil', 'UsuariosController@doCambiarPerfil')->name('usuarios.do-cambiar-perfil');
});

Route::prefix('ventas')->group(function () {
    Route::get('/{id?}/json', [VentasController::class, 'showJson'])->name('ventas.show-json');
    Route::post('/create', [VentasController::class, 'create'])->name('ventas.create');
    Route::post('/tiempo', [ReportesController::class, 'ventasTiempoJson']);
    Route::post('/vendedor', [ReportesController::class, 'ventasVendedorJson']);
    Route::post('/producto', [ReportesController::class, 'ventasProductoJson']);
    Route::get('/hoy', [ReportesController::class, 'ventas'])->name('ventas.reporte');
    Route::view('/productos', 'reportes.ventas-productos')->name('ventas.productos');
    Route::get('/historico', [ReportesController::class, 'ventasTiempo'])->name('ventas-history.reporte');
    Route::get('/ultima', [VentasController::class, 'ultima'])->name('ventas.reprint-last');
    Route::post('/productos-reportar', [ReportesController::class, 'buscarMenosVendidos'])->name('ventas-productos.reportar');
    Route::post('/tiempo-reportar', [ReportesController::class, 'buscarPorMes'])->name('ventas-tiempo.reportar');
});

Route::prefix('reportes')->middleware('auth')->group(function () {
    Route::view('estadisticas', 'reportes.estadisticas')->name('reportes.estadisticas');
});


Route::prefix('pago-servicios')->group(function () {
    Route::view('recargas', 'pago-servicios.recargas')->name('pago-servicios.recargas');
    Route::view('servicios', 'pago-servicios.servicios')->name('pago-servicios.servicios');
});

Route::prefix('transferencias')->group(function () {
    Route::view('create', 'transferencias.create')->name('transferencias.create');
});

Route::prefix('inventario')->group(function () {
    Route::view('create', 'inventarios.create')->name('inventario.create');
    Route::view('diff', 'inventarios.diff');
});

Route::prefix('aperturas-caja')->group(function () {
    Route::view('', 'aperturas-caja.index')->name('aperturas-caja.index')->can('aperturas-caja.view');
});

Route::prefix('ordenes-compra')->group(function () {
    Route::view('', 'ordenes-compra.index')->name('ordenes-compra.index')->can('ordenes-compra.view');
    Route::view('faltantes', 'ordenes-compra.faltantes')
        ->name('ordenes-compra.faltantes')
        ->can('ordenes-compra-faltantes.view');
});

// Don't delete, this is for reporting
Route::get('t/{ini}/{fin}', function ($ini, $fin) {
    return Excel::download(new VentasExport($ini, $fin), 'ventas' . date('YmdHisu') . '.xlsx');
    //return $result;
});

Route::get('excel/ventas-x-producto/{ini}/{fin}', function ($ini, $fin) {
    return Excel::download(new VentasXProductoExport($ini, $fin), 'ventasXProducto' . date('YmdHisu') . '.xlsx');
    //return $result;
});
Route::get('test', 'ReportesController@ventasProductoJson');

Route::get('maintenance', function() {
    Artisan::call('cache:clear');
});
