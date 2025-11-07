<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profile;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            [
                'name' => 'Administrador',
                'description' => 'Acesso total ao sistema.',
            ],
            [
                'name' => 'Usuario',
                'description' => 'Usuários pode realizar operações básicas do sistema.',
            ],
            [
                'name' => 'Operador',
                'description' => 'Operações básicas do sistema',
            ]
        ];
        foreach ($profiles as $profile) {
            Profile::firstOrCreate(
                ['name' => $profile['name']],
                $profile
            );
        }
    }
}