<?php

use App\Http\Controllers\ApartadosController;
use App\Http\Controllers\AperturasCajaController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\OrdenesCompraController;
use App\Http\Controllers\PagoServiciosController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProductosTagsController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\SimilaresController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\TransferenciasController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\VentasProductosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return ["user" => $user, 'rol' => $user->roles[0]];
    });

    Route::prefix('pago-servicios')->group(function () {
        Route::post('recargas', [PagoServiciosController::class, 'recargas']);
        Route::post('servicios', [PagoServiciosController::class, 'servicios']);
    });

    Route::prefix('transferencias')->group(function () {
        Route::post('', [TransferenciasController::class, 'store']);
    });

    Route::prefix('gastos')->group(function () {
        Route::post('', [GastosController::class, 'store']);
    });

    Route::prefix('productos')->group(function () {
        // TODO: Migrate all productos module to API-Vue
        Route::get('/{id}/proveedores', [ProductosController::class, 'proveedores']);
        Route::post('/{id}/proveedores', [ProductosController::class, 'proveedoresStore']);
        Route::put('/{id}/proveedores/{proveedor_id}', [ProductosController::class, 'proveedoresUpdate']);
        Route::delete('/{id}/proveedores/{proveedor_id}', [ProductosController::class, 'proveedoresDelete']);
        Route::apiResource('/{producto}/tags', ProductosTagsController::class);
        Route::get('/temp/json', [ProductosController::class, 'indexJson']);
    });

    Route::prefix('similares')->group(function () {
        Route::post('', [SimilaresController::class, 'store']);
        Route::get('/{id}', [SimilaresController::class, 'index']);
    });

    Route::prefix('ventas')->group(function () {
        Route::get('/reportar', [VentasController::class, 'reportar']);
        Route::apiResource('/{venta}/productos', VentasProductosController::class);
    });

    Route::apiResource('ventas', VentasController::class);

    Route::get('inventario/diff', [InventarioController::class, 'diff']);
    Route::get('inventario/finish', [InventarioController::class, 'finish']);

    Route::apiResource('inventario', InventarioController::class);
    Route::apiResource('aperturas-caja', AperturasCajaController::class);
    Route::apiResource('ordenes-compra', OrdenesCompraController::class);
    Route::apiResource('proveedores', ProveedoresController::class);
    Route::apiResource('productos', ProductosController::class)->except('show');
    Route::apiResource('tags', TagsController::class)->except(['show', 'store', 'update', 'destroy']);

    // TODO: This is temporally, migrate categorias to Vue
    Route::get('categorias', [CategoriasController::class, 'indexApi']);

    Route::prefix('proveedores')->group(function () {
        Route::get('extract/prices/{codigo}', [ProveedoresController::class, 'extractPrice']);
    });

    Route::prefix('ordenes-compra')->group(function () {
        Route::get('faltantes/suggested/{id?}', [OrdenesCompraController::class, 'suggested']);
        Route::get('faltantes/not-available/{id?}', [OrdenesCompraController::class, 'notAvailable']);
        Route::post('items/add', [OrdenesCompraController::class, 'addItem']);
        Route::patch('{ocId}/items/{productoId}', [OrdenesCompraController::class, 'patchItem']);
        Route::delete('{ocId}/items/{productoId}', [OrdenesCompraController::class, 'deleteItem']);
    });

    Route::prefix('inventario')->group(function () {
        Route::get('diff', [InventarioController::class, 'diff']);
        Route::get('finish', [InventarioController::class, 'finish']);
    });

    Route::apiResource('apartados', ApartadosController::class);
    Route::apiResource('proveedores', ProveedoresController::class);
});
