<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * EXECUTANDO TESTES PARA O PRO'JETO
     */
    public function run(): void
    {
        $this->call([
            ProfileSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
        ]);
    }
}