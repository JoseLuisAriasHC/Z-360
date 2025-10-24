<?php

namespace Database\Seeders;

use App\Models\Etiqueta;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EtiquetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $etiquetas = [
            // Promocionales y de Venta
            ['nombre' => 'Más Vendidos'],
            ['nombre' => 'Nueva Colección'],
            ['nombre' => 'Edición Limitada'],
            ['nombre' => 'Últimos Pares'],
            ['nombre' => 'En Oferta'],
            ['nombre' => 'Rebajas Finales'],
            ['nombre' => 'Exclusivo Online'],

            // Características y Usos
            ['nombre' => 'Impermeable'],
            ['nombre' => 'Amortiguación Superior'],
            ['nombre' => 'Piel Genuina'],
            ['nombre' => 'Tejido Transpirable'],
            ['nombre' => 'Deportivas'],
            ['nombre' => 'Casual'],
        ];

        foreach ($etiquetas as $etiqueta) {
            Etiqueta::create($etiqueta);
        }

        $this->command->info('Etiquetas creadas');
        
    }
}
