<?php

use App\Http\Controllers\admin\OrderControllerADM;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\admin\ColorControllerADM;
use App\Http\Controllers\admin\CuponControllerADM;
use App\Http\Controllers\admin\DashboardControllerADM;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\admin\EtiquetaControllerADM;
use App\Http\Controllers\admin\MarcaControllerADM;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\admin\ProductControllerADM;
use App\Http\Controllers\admin\ProductDetailControllerADM;
use App\Http\Controllers\admin\ProductUsageControllerADM;
use App\Http\Controllers\admin\ProductVariantControllerADM;
use App\Http\Controllers\admin\TallaControllerADM;
use App\Http\Controllers\UserBrandSizeController;
use App\Http\Controllers\admin\UserPhotoControllerADM;
use App\Http\Controllers\admin\VariantImageControllerADM;
use App\Http\Controllers\admin\VariantSizeControllerADM;
use App\Http\Controllers\admin\WebSettingsControllerADM;
use App\Http\Controllers\ProductListController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas para la Autenticacion
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::prefix('products')->group(function () {
    Route::get('/top-ventas', [ProductListController::class, 'topVentas']);
    Route::get('/ofertas', [ProductListController::class, 'ofertas']);
    Route::get('/novedades', [ProductListController::class, 'novedades']);
    Route::get('/listado', [ProductListController::class, 'listado']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Rutas para el carrito
    Route::get('cart', [CartController::class, 'getCart']);
    Route::post('cart/add', [CartController::class, 'addItem']);
    Route::put('cart/item/{item}', [CartController::class, 'updateItem']);
    Route::delete('cart/item/{item}', [CartController::class, 'removeItem']);
    Route::delete('cart/clear', [CartController::class, 'clearCart']);

    // Rutas para las direcciones
    Route::apiResource('direcciones', DireccionController::class)->parameters(['direcciones' => 'direccion']);

    // Rutas para las tallas perzonalizadas de los usuarios
    Route::get('/user-brand-size/{userId}', [UserBrandSizeController::class, 'index']);
    Route::post('/user-brand-size/bulk', [UserBrandSizeController::class, 'bulk']);
});

// Rutas para los pedidos
Route::prefix('orders')->group(function () {
    // Usuario autenticado
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/last', [OrderController::class, 'showLastOrder']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::post('/from-cart', [OrderController::class, 'storeFromCart']);
    });

    // Invitado
    Route::get('/token/{token}', [OrderController::class, 'showByToken']);
    Route::post('/guest', [OrderController::class, 'storeGuest']);

    Route::post('/{order}/confirmar-pago', [OrderController::class, 'confirmarPago']);
});

/*
|--------------------------------------------------------------------------
| Routes ADM
|--------------------------------------------------------------------------
|
| Todas las rutas que se usaran SOLO en la parte de la administración
| Solo podran acceder usuarios autenticados y que sean administradores
|
*/

Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    // Rutas para las variantes de los productos
    Route::get('/products/{productId}/variants', [ProductVariantControllerADM::class, 'index']);
    Route::get('/product-variants/{product_variant}', [ProductVariantControllerADM::class, 'show']);
    Route::put('/product-variants/{product_variant}', [ProductVariantControllerADM::class, 'update']);
    Route::post('product-variants/delete-multiple', [ProductVariantControllerADM::class, 'destroyMultiple']);
    Route::delete('/product-variants/{product_variant}', [ProductVariantControllerADM::class, 'destroy']);
    Route::post('/product-variants/generate', [ProductVariantControllerADM::class, 'generate']);

    Route::get('/product-variants/{product_variant}/images', [VariantImageControllerADM::class, 'getVariantImages']);
    Route::post('product-variants/{product_variant}/images', [VariantImageControllerADM::class, 'uploadImages']);
    Route::delete('/variant-images/{variant_image}', [VariantImageControllerADM::class, 'deleteImage']);

    // Rutas para las variantes de tallas
    Route::delete('variant-sizes/{variant_size}', [VariantSizeControllerADM::class, 'destroy']);
    Route::post('variant-sizes/{productVariant}/create', [VariantSizeControllerADM::class, 'createVarianteSize']);
    Route::put('/variant-sizes/{variant_size}', [VariantSizeControllerADM::class, 'updateVarianteSize']);
    Route::get('/variant-sizes/{product_variant}/sizes', [VariantSizeControllerADM::class, 'getVariantSizes']);

    // Rutas para los detalles de un producto
    Route::get('products/{product}/details', [ProductDetailControllerADM::class, 'show']);
    Route::put('products/{product}/details', [ProductDetailControllerADM::class, 'update']);

    // Ruta para las etiquetas de un producto
    Route::post('etiquetas/delete-multiple', [EtiquetaControllerADM::class, 'destroyMultiple']);
    Route::get('/products/{product}/etiquetas', [EtiquetaControllerADM::class, 'getEtiquetasByProduct']);
    Route::post('/products/{product}/etiquetas', [EtiquetaControllerADM::class, 'asignarEtiquetas']);
    Route::delete('/etiquetas/{etiqueta}/limpiar', [EtiquetaControllerADM::class, 'limpiarEtiqueta']);
    Route::apiResource('etiquetas', EtiquetaControllerADM::class);

    // Ruta para los usos de un producto
    Route::post('usages/delete-multiple', [ProductUsageControllerADM::class, 'destroyMultiple']);
    Route::get('products/{product}/usages', [ProductUsageControllerADM::class, 'showProductUsages']);
    Route::post('products/{product}/usages', [ProductUsageControllerADM::class, 'asignarUsos']);
    Route::apiResource('usages', ProductUsageControllerADM::class);

    // Rutas para el Producto Base
    Route::post('products/delete-multiple', [ProductControllerADM::class, 'destroyMultiple']);
    Route::get('/products/search', [ProductControllerADM::class, 'search']);
    Route::apiResource('products', ProductControllerADM::class);

    // Rutas para los Pedidos
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderControllerADM::class, 'index']);
        Route::get('/{order}', [OrderControllerADM::class, 'show']);
        Route::patch('/{order}/status', [OrderControllerADM::class, 'updateStatus']);
    });

    // Rutas para la gestion de las fotos de los usuarios
    Route::apiResource('user-photos', UserPhotoControllerADM::class)->except(['update']);
    Route::patch('user-photos/{userPhoto}/aprobar', [UserPhotoControllerADM::class, 'aprobar']);
    Route::patch('user-photos/{userPhoto}/descartar', [UserPhotoControllerADM::class, 'descartar']);
    Route::delete('user-photos/{product_id}/eliminar-no-aprobadas', [UserPhotoControllerADM::class, 'eliminarNoAprobadas']);

    // Rutas para las configuraciones de la aplicacion (IVA, Coste de envio, cuando es gratis el coste de envio, etc) 
    Route::get('/web-settings/empresa', [WebSettingsControllerADM::class, 'getEmpresaSettings']);
    Route::get('/web-settings/envio', [WebSettingsControllerADM::class, 'getEnvioSettings']);
    Route::get('/web-settings/ecommerce', [WebSettingsControllerADM::class, 'getEcommerceSettings']);
    Route::patch('/web-settings/save-empresa', [WebSettingsControllerADM::class, 'saveEmpresaSettings']);
    Route::patch('/web-settings/save-envio', [WebSettingsControllerADM::class, 'saveEnvioSettings']);
    Route::patch('/web-settings/save-ecommerce', [WebSettingsControllerADM::class, 'saveEcommerceSettings']);
    Route::patch('/web-settings/{clave}', [WebSettingsControllerADM::class, 'update']);

    // Ruta para los Colores
    Route::post('colores/delete-multiple', [ColorControllerADM::class, 'destroyMultiple']);
    Route::apiResource('colores', ColorControllerADM::class)->parameters(['colores' => 'color']);
    // Ruta para los Cupones
    Route::post('cupones/delete-multiple', [CuponControllerADM::class, 'destroyMultiple']);
    Route::apiResource('cupones', CuponControllerADM::class)->parameters(['cupones' => 'cupon']);
    // Ruta para las marcas
    Route::post('marcas/delete-multiple', [MarcaControllerADM::class, 'destroyMultiple']);
    Route::apiResource('marcas', MarcaControllerADM::class);
    // Ruta para las tallas
    Route::post('tallas/delete-multiple', [TallaControllerADM::class, 'destroyMultiple']);
    Route::apiResource('tallas', TallaControllerADM::class);
    // Ruta para las metricas del dashboard
    Route::get('/dashboard/metricas', [DashboardControllerADM::class, 'getMetricas']);
    // Ruta para obtener los 10 productos más vendidos
    Route::get('/dashboard/top-ventas', [DashboardControllerADM::class, 'getTopVentasProducts']);
    // Ruta para obtener la grafica del performance de cada mes
    Route::get('/dashboard/weekly-performance', [DashboardControllerADM::class, 'getWeeklyPerformance']);
});

// Ruta del webhook de stripe para capturar los eventos en el proceso de pago que queremos
Route::post('/stripe/webhook', [OrderController::class, 'stripeWebhook']);
