<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas = [
            ['nombre' => 'Nike', 'logo' => '#/logos/nike.svg', 'talla_offset' => 0.00],
            ['nombre' => 'Adidas', 'logo' => '#/logos/adidas.svg', 'talla_offset' => 0.50],
            ['nombre' => 'Puma', 'logo' => '#/logos/puma.svg', 'talla_offset' => -0.25],
            ['nombre' => 'Converse', 'logo' => '#/logos/converse.svg', 'talla_offset' => 1.00],
            ['nombre' => 'Vans', 'logo' => '#/logos/vans.svg', 'talla_offset' => -0.50],
            ['nombre' => 'New Balance', 'logo' => '#/logos/new_balance.svg', 'talla_offset' => 0.25],
            ['nombre' => 'Asics', 'logo' => '#/logos/asics.svg', 'talla_offset' => -0.5],
            ['nombre' => 'Hoka', 'logo' => '#/logos/hoka.svg', 'talla_offset' => 1.50],
            ['nombre' => 'Lacoste', 'logo' => '#/logos/lacoste.svg', 'talla_offset' => -1.00],
            ['nombre' => 'Jordan', 'logo' => '#/logos/jordan.svg', 'talla_offset' => -0.5],
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }
        $this->command->info('Marcas creadas');

    }
}
