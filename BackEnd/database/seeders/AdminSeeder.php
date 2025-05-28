<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@ejemplo.com', // Reemplaza con el correo electrónico de administrador que desees
            'password' => Hash::make('Boca_1981'), // Reemplaza la contraseña con la que desees
        ]);
    }
}
