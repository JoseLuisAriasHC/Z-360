<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('token')->nullable()->index();
            $table->string('cupon_codigo', 50)->nullable();
            $table->enum('estado', ['pendiente','procesando','enviado','entregado','cancelado'])->default('pendiente');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('costo_envio', 8, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('nombre_cliente')->nullable();
            $table->string('email_cliente')->nullable();
            $table->string('telefono_cliente')->nullable();
            $table->string('direccion_calle')->nullable();
            $table->string('direccion_numero_calle')->nullable();
            $table->string('direccion_piso_info')->nullable();
            $table->string('direccion_ciudad')->nullable();
            $table->string('direccion_cp')->nullable();
            $table->enum('metodo_pago', ['tarjeta','paypal','otro'])->nullable();
            $table->dateTime('fecha')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
