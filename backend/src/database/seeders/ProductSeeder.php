<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Marca;
use App\Enums\TipoProducto;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Obtener IDs de las marcas disponibles
        $marcaIds = Marca::pluck('id')->toArray();
        if (empty($marcaIds)) {
            $this->command->warn('No se encontraron marcas. Ejecuta el MarcaSeeder primero.');
            return;
        }

        $allTypes = TipoProducto::cases();
        
        // 2. Crear 15 productos 'urbanas'
        Product::factory()->count(48)->create([
            'tipo' => TipoProducto::URBANAS,
            'marca_id' => fn () => fake()->randomElement($marcaIds),
        ]);

        // 3. Crear 5 productos de los tipos restantes
        foreach ($allTypes as $tipo) {
            if ($tipo !== TipoProducto::URBANAS) {
                Product::factory()->count(22)->create([
                    'tipo' => $tipo,
                    'marca_id' => fn () => fake()->randomElement($marcaIds),
                ]);
            }
        }
        
        $this->command->info('Creados 15 productos Urbanas y 5 de cada uno de los 3 tipos restantes.');
    }
}
