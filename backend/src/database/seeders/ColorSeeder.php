<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colores = [
            ['nombre' => 'Negro', 'codigo_hex' => '#000000'],
            ['nombre' => 'Blanco', 'codigo_hex' => '#FFFFFF'],
            ['nombre' => 'Rojo', 'codigo_hex' => '#e7352b'],
            ['nombre' => 'Azul', 'codigo_hex' => '#1790c8'],
            ['nombre' => 'Verde', 'codigo_hex' => '#7bba3c'],
            ['nombre' => 'Amarillo', 'codigo_hex' => '#fed533'],
            ['nombre' => 'Gris', 'codigo_hex' => '#808080'],
            ['nombre' => 'Marrón', 'codigo_hex' => '#964B00'],
            ['nombre' => 'Azul Marino', 'codigo_hex' => '#000080'],
            ['nombre' => 'Burdeos', 'codigo_hex' => '#800020'],
            ['nombre' => 'Beige', 'codigo_hex' => '#F5F5DC'],
            ['nombre' => 'Rosa', 'codigo_hex' => '#f0728f'],
            ['nombre' => 'Naranja', 'codigo_hex' => '#f36b26'],
            ['nombre' => 'Violeta', 'codigo_hex' => '#8d429f'],
            ['nombre' => 'Plata', 'codigo_hex' => '#C0C0C0'],
        ];

        foreach ($colores as $color) {
            Color::create($color);
        }
    }
}
