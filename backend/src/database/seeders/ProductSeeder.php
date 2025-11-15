<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Marca;
use App\Enums\TipoProducto;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de las marcas disponibles
        $marcaIds = Marca::pluck('id')->toArray();
        if (empty($marcaIds)) {
            $this->command->warn('No se encontraron marcas. Ejecuta el MarcaSeeder primero.');
            return;
        }

        $allTypes = TipoProducto::cases();
        
        // Crear 48 productos 'urbanas'
        Product::factory()->count(48)->create([
            'tipo' => TipoProducto::URBANAS,
            'marca_id' => fn () => $marcaIds[array_rand($marcaIds)],
        ]);

        // Crear 22 productos de los tipos restantes
        foreach ($allTypes as $tipo) {
            if ($tipo !== TipoProducto::URBANAS) {
                Product::factory()->count(22)->create([
                    'tipo' => $tipo,
                    'marca_id' => fn () => $marcaIds[array_rand($marcaIds)],
                ]);
            }
        }
        
        $this->command->info('Productos creados exitosamente.');
    }
}