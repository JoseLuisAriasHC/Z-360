<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\CuponController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\EtiquetaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\ProductUsageController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\TallaController;
use App\Http\Controllers\UserBrandSizeController;
use App\Http\Controllers\UserPhotoController;
use App\Http\Controllers\WebSettingsController;
use Illuminate\Http\Request;
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

// AuthController
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Productos
Route::apiResource('products', ProductController::class);
Route::get('/products/search', [ProductController::class, 'search']);

// ProductDetailController
Route::get('products/{product}/details', [ProductDetailController::class, 'show']);
Route::put('products/{product}/details', [ProductDetailController::class, 'update']);

// ProductVariantController
Route::get('/products/{product}/variants', [ProductVariantController::class, 'index']);
Route::get('/product-variants/{product_variant}', [ProductVariantController::class, 'show']);
Route::put('/product-variants/{product_variant}', [ProductVariantController::class, 'update']);
Route::delete('/product-variants/{product_variant}', [ProductVariantController::class, 'destroy']);
Route::post('/product-variants/generate', [ProductVariantController::class, 'generate']);

// MarcaController
Route::apiResource('marcas', MarcaController::class);

// ColorController
Route::apiResource('colores', ColorController::class)->parameters(['colores' => 'color']);

// TallaController
Route::apiResource('tallas', TallaController::class);

// UserBrandSizeController
Route::get('/user-brand-size/{userId}', [UserBrandSizeController::class, 'index']);
Route::post('/user-brand-size/bulk', [UserBrandSizeController::class, 'bulk']);

// UserPhotoController
Route::apiResource('user-photos', UserPhotoController::class)->except(['update']);
Route::patch('user-photos/{userPhoto}/aprobar', [UserPhotoController::class, 'aprobar']);
Route::patch('user-photos/{userPhoto}/descartar', [UserPhotoController::class, 'descartar']);
Route::delete('user-photos/{product_id}/eliminar-no-aprobadas', [UserPhotoController::class, 'eliminarNoAprobadas']);

// WebSettingsController
Route::get('/web-settings', [WebSettingsController::class, 'index']);
Route::get('/web-settings/{web_settings}', [WebSettingsController::class, 'show']);
Route::patch('/web-settings/{clave}', [WebSettingsController::class, 'update']);

// EtiquetaController
Route::apiResource('etiquetas', EtiquetaController::class);
Route::post('/products/{product}/etiquetas', [EtiquetaController::class, 'asignarEtiquetas']);
Route::delete('/products/{product}/etiquetas', [EtiquetaController::class, 'eliminarEtiquetas']);
Route::delete('/etiquetas/{etiqueta}/limpiar', [EtiquetaController::class, 'limpiarEtiqueta']);

// ProductUsageController
Route::apiResource('usages', ProductUsageController::class);
Route::get('products/{product}/usages', [ProductUsageController::class, 'showProductUsages']);
Route::post('products/{product}/usages', [ProductUsageController::class, 'asignarUsos']);

// CuponController
Route::apiResource('cupones', CuponController::class)->parameters([
    'cupones' => 'cupon'
]);

Route::apiResource('direcciones', DireccionController::class)->parameters([
    'direcciones' => 'direccion'
])->middleware('auth:sanctum');

// CartControlle
Route::middleware('auth:sanctum')->group(function () {
    Route::get('cart', [CartController::class, 'getCart']);
    Route::post('cart/add', [CartController::class, 'addItem']);
    Route::put('cart/item/{item}', [CartController::class, 'updateItem']);
    Route::delete('cart/item/{item}', [CartController::class, 'removeItem']);
    Route::delete('cart/clear', [CartController::class, 'clearCart']);
});

Route::get('/me', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
