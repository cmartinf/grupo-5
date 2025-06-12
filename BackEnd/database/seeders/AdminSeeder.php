<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Para evitar duplicados, mejor usar updateOrCreate
        User::updateOrCreate(
            ['email' => 'programacion@vanguardia'],
            [
                'name' => 'Programación',
                'password' => Hash::make('grupo5'),
                'email_verified_at' => now(),
            ]
        );
    }
}
