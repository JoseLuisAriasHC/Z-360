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
            ['nombre' => 'Reebok', 'logo' => '#/logos/reebok.svg', 'talla_offset' => -0.5],
            ['nombre' => 'Skechers', 'logo' => '#/logos/skechers.svg', 'talla_offset' => 1.50],
            ['nombre' => 'Timberland', 'logo' => '#/logos/timberland.svg', 'talla_offset' => -1.00],
            ['nombre' => 'Clarks', 'logo' => '#/logos/clarks.svg', 'talla_offset' => -0.5],
        ];

        foreach ($marcas as $marca) {
            Marca::create($marca);
        }
    }
}
