<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = [
            [
                'cep' => '40000000',
                'logradouro' => 'Rua da Paz',
                'numero' => '100',
                'complemento' => 'Apto 201',
                'bairro' => 'Centro',
                'cidade' => 'Salvador',
                'estado' => 'BA',
            ],
            [
                'cep' => '41000000',
                'logradouro' => 'Avenida Principal',
                'numero' => '500',
                'complemento' => 'Sala 10',
                'bairro' => 'Barra',
                'cidade' => 'Salvador',
                'estado' => 'BA',
            ],
            [
                'cep' => '42000000',
                'logradouro' => 'Rua das Flores',
                'numero' => '250',
                'complemento' => null,
                'bairro' => 'Pituba',
                'cidade' => 'Salvador',
                'estado' => 'BA',
            ],
        ];

        foreach ($addresses as $addressData) {
            $address = Address::firstOrCreate(
                [
                    'cep' => $addressData['cep'],
                    'numero' => $addressData['numero']
                ],
                $addressData
            );

            // Vincular endereço a usuários aleatórios
            $users = User::inRandomOrder()->limit(2)->get();
            foreach ($users as $user) {
                $user->addresses()->syncWithoutDetaching([$address->id]);
            }
        }
    }
}