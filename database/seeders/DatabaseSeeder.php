<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Funcionário de teste
        User::create([
            'name'     => 'Funcionário Teste',
            'email'    => 'funcionario@loja.com',
            'password' => Hash::make('senha123'),
            'role'     => 'funcionario',
        ]);

        // Admin de teste
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@loja.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // Produtos de exemplo
        $produtos = [
            ['name' => 'Camiseta Básica',      'price' => 49.90,  'quantity' => 30,  'description' => 'Camiseta 100% algodão'],
            ['name' => 'Calça Jeans',           'price' => 129.90, 'quantity' => 15,  'description' => 'Calça slim fit'],
            ['name' => 'Tênis Casual',          'price' => 199.90, 'quantity' => 8,   'description' => 'Solado de borracha'],
            ['name' => 'Boné Aba Reta',         'price' => 39.90,  'quantity' => 4,   'description' => 'Bordado frontal'],
            ['name' => 'Meia Kit com 3',        'price' => 19.90,  'quantity' => 50,  'description' => 'Algodão respirável'],
            ['name' => 'Jaqueta Corta-Vento',   'price' => 249.90, 'quantity' => 0,   'description' => 'Impermeável'],
        ];

        foreach ($produtos as $produto) {
            Product::create($produto);
        }
    }
}
