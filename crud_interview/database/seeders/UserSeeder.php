<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminProfile = Profile::where('name', 'Administrador')->first();
        $gerenteProfile = Profile::where('name', 'Usuario')->first();
        $users = [
            [
                'name' => 'Admin Sistema',
                'email' => 'admin@sistema.com',
                'cpf' => '12345678901',
                'profile_id' => $adminProfile->id,
                'password' => bcrypt('password123'),
            ],
            [
                'name' => 'João Silva',
                'email' => 'joao@email.com',
                'cpf' => '98765432109',
                'profile_id' => $gerenteProfile->id,
                'password' => bcrypt('password123'),
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@email.com',
                'cpf' => '45678912345',
                'profile_id' => $gerenteProfile->id,
                'password' => bcrypt('password123'),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}