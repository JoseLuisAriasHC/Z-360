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
            $table->renameColumn('nombre_cliente', 'envio_nombre');
            $table->renameColumn('email_cliente', 'envio_email');
            $table->renameColumn('telefono_cliente', 'envio_telefono');
            $table->renameColumn('direccion_calle', 'envio_direccion_calle');
            $table->renameColumn('direccion_numero_calle', 'envio_direccion_numero_calle');
            $table->renameColumn('direccion_piso_info', 'envio_direccion_piso_info');
            $table->renameColumn('direccion_ciudad', 'envio_direccion_ciudad');
            $table->renameColumn('direccion_cp', 'envio_direccion_cp');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('facturacion_nombre')->after('envio_direccion_cp');
            $table->string('facturacion_email')->after('facturacion_nombre');
            $table->string('facturacion_telefono')->after('facturacion_email');
            $table->string('facturacion_direccion_calle')->after('facturacion_telefono');
            $table->string('facturacion_direccion_numero_calle')->after('facturacion_direccion_calle');
            $table->string('facturacion_direccion_piso_info')->nullable()->after('facturacion_direccion_numero_calle');
            $table->string('facturacion_direccion_ciudad')->after('facturacion_direccion_piso_info');
            $table->string('facturacion_direccion_cp')->after('facturacion_direccion_ciudad');

            $table->boolean('usar_misma_direccion_facturacion')->default(true)->after('facturacion_direccion_cp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'facturacion_nombre',
                'facturacion_email',
                'facturacion_telefono',
                'facturacion_direccion_calle',
                'facturacion_direccion_numero_calle',
                'facturacion_direccion_piso_info',
                'facturacion_direccion_ciudad',
                'facturacion_direccion_cp',
                'usar_misma_direccion_facturacion',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            // Restaurar nombres originales
            $table->renameColumn('envio_nombre', 'nombre_cliente');
            $table->renameColumn('envio_email', 'email_cliente');
            $table->renameColumn('envio_telefono', 'telefono_cliente');
            $table->renameColumn('envio_direccion_calle', 'direccion_calle');
            $table->renameColumn('envio_direccion_numero_calle', 'direccion_numero_calle');
            $table->renameColumn('envio_direccion_piso_info', 'direccion_piso_info');
            $table->renameColumn('envio_direccion_ciudad', 'direccion_ciudad');
            $table->renameColumn('envio_direccion_cp', 'direccion_cp');
        });
    }
};
