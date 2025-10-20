<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TallaSeeder::class,
            ColorSeeder::class,
            MarcaSeeder::class,
            WebSettingsSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
