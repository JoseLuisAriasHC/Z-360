<?php

namespace Database\Seeders;

use App\Models\Talla;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TallaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tallas = [
            36, 36.5, 37, 37.5, 38, 38.5, 39, 39.5, 40, 40.5,
            41, 41.5, 42, 42.5, 43, 43.5, 44, 44.5, 45, 45.5,
            46, 46.5, 47, 47.5, 48, 48.5, 49, 49.5, 50, 50.5,
            51, 51.5, 52, 52.5, 53, 53.5, 54, 54.5, 55, 55.5,
        ];

        foreach ($tallas as $numero) {
            Talla::create([
                'numero' => $numero
            ]);
        }
    }
}
