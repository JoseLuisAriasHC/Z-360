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
                'valor'       => '5.99',
                'nombre'      => 'Coste de envío',
                'descripcion' => 'Coste fijo de envío para pedidos por debajo del mínimo.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'free_coste_envio_from',
                'valor'       => '100',
                'nombre'      => 'Envío gratis desde',
                'descripcion' => 'Cantidad mínima para que el envío sea gratuito.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'email_admin',
                'valor'       => 'devjoseluisariashc@gmail.com',
                'nombre'      => 'Correo del admnistrador',
                'descripcion' => 'Correo del admnistrador para obtener reportes.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'iva',
                'valor'       => '21',
                'nombre'      => 'IVA',
                'descripcion' => 'Impuestos que se aplican a los productos',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'empresa_nombre',
                'valor'       => 'Z-360',
                'nombre'      => 'Nombre',
                'descripcion' => 'Nombre de la empresa',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'empresa_direccion',
                'valor'       => 'Casco Antiguo, 50004 Zaragoza',
                'nombre'      => 'Direccion',
                'descripcion' => 'Direeción donde se encuentra la empresa',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'empresa_telefono',
                'valor'       => '+34 666 777 888 999',
                'nombre'      => 'Telefono',
                'descripcion' => 'Telefono de la empresa',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'clave'       => 'empresa_email',
                'valor'       => 'devjoseluisariashc@gmail.com',
                'nombre'      => 'Correo electronico',
                'descripcion' => 'Correo electronico de la empresa',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        $this->command->info('Ajustes de la WEB creados');
    }
}
