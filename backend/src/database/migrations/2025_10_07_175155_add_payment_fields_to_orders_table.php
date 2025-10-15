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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('pago_id')->nullable()->after('metodo_pago');
            $table->string('pago_estado')->default('pending')->after('pago_id');
            $table->timestamp('pago_fecha')->nullable()->after('pago_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['pago_id', 'pago_estado', 'pago_fecha']);
        });
    }
};
