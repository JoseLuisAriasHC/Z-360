<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WebSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('web_settings')->insert([
            [
                'clave'       => 'max_fotos_producto_por_usuario',
                'valor'       => '4',
                'nombre'      => 'Máximo de fotos por producto',
                'descripcion' => 'Número máximo de fotos que un usuario puede subir por cada producto.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'coste_envio',
                'valor'       => '9.99',
                'nombre'      => 'Coste de envío',
                'descripcion' => 'Coste fijo de envío para pedidos por debajo del mínimo.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'free_coste_envio_from',
                'valor'       => '120',
                'nombre'      => 'Envío gratis desde',
                'descripcion' => 'Cantidad mínima para que el envío sea gratuito.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}
