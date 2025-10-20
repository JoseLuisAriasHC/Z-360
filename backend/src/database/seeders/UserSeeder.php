<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nombre' => 'Jose Luis',
            'apellido' => 'Arias HC',
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'password')), 
            'telefono' => '123456789',
            'rol' => 'admin',
        ]);

        User::create([
            'nombre' => 'María',
            'apellido' => 'Gómez',
            'email' => 'cliente@example.com',
            'password' => Hash::make('password'),
            'telefono' => '987654321',
            'rol' => 'cliente',
        ]);
    }
}
