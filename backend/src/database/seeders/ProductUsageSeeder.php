<?php

namespace Database\Seeders;

use App\Models\ProductUsage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductUsageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usos = [
            // Estilos generales / Moda
            ['nombre' => 'Lifestyle'],
            ['nombre' => 'Casual'],
            ['nombre' => 'Urbano'],

            // Deportes específicos / Rendimiento
            ['nombre' => 'Running'],
            ['nombre' => 'Baloncesto'],
            ['nombre' => 'Fútbol'],
            ['nombre' => 'Gym y Training'],
            ['nombre' => 'Skateboard'],
            ['nombre' => 'Senderismo'],

            // Submarcas / Colecciones icónicas
            ['nombre' => 'Jordan'],
            ['nombre' => 'Originals'], // Común en marcas como Adidas
            ['nombre' => 'Clásico Retro'],
        ];

        foreach ($usos as $uso) {
            ProductUsage::create($uso);
        }

        $this->command->info('Usos del producto creados');
    }
}
