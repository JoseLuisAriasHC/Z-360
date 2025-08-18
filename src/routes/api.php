<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductVariantController;
use App\Http\Controllers\TallaController;
use App\Http\Controllers\VariantImageController;
use App\Http\Controllers\VariantSizeController;
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
Route::apiResource('variant-sizes', VariantSizeController::class);
Route::apiResource('variant-images', VariantImageController::class);

// ProductVariantController
Route::apiResource('product-variants', ProductVariantController::class)->except(['store']);
Route::post('/product-variants/generate', [ProductVariantController::class, 'generate']);

// MarcaController
Route::apiResource('marcas', MarcaController::class);

// ColorController
Route::apiResource('colores', ColorController::class)->parameters(['colores' => 'color']);

// TallaController
Route::apiResource('tallas', TallaController::class);

Route::get('/me', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
