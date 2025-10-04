<?php

use App\Models\Order;
use App\Models\WebSettings;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $settings = [
        'iva' => (float) WebSettings::getValue('iva', 21),
        'empresa_nombre' => WebSettings::getValue('empresa_nombre', 'Z-360'),
        'empresa_direccion' => WebSettings::getValue('empresa_direccion', 'Casco Antiguo, 50004 Zaragoza'),
        'empresa_telefono' => WebSettings::getValue('empresa_telefono', '+34 666 777 888 999'),
        'empresa_email' => WebSettings::getValue('empresa_email', 'z360@gmail.com'),
    ];
    $order = Order::latest()->first();

    return view('emails.factura', compact('order', 'settings'));
});
