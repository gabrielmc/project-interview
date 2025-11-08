<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProfileSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
        ]);
    }
}