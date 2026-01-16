<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::create([
            'name' => 'Admin Principal',
            'email' => 'admin@ecommerce.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);

        // Manager
        User::create([
            'name' => 'Manager Commerce',
            'email' => 'manager@ecommerce.com',
            'password' => Hash::make('manager123'),
            'email_verified_at' => now(),
        ]);

        // Vendeur
        User::create([
            'name' => 'Vendeur Produits',
            'email' => 'vendeur@ecommerce.com',
            'password' => Hash::make('vendeur123'),
            'email_verified_at' => now(),
        ]);

        // Client test 1
        User::create([
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Client test 2
        User::create([
            'name' => 'Marie Martin',
            'email' => 'marie.martin@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Client test 3
        User::create([
            'name' => 'Pierre Bernard',
            'email' => 'pierre.bernard@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Client test 4
        User::create([
            'name' => 'Sophie Dubois',
            'email' => 'sophie.dubois@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Client test 5
        User::create([
            'name' => 'Luc Thomas',
            'email' => 'luc.thomas@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        // Utilisateur de test API
        User::create([
            'name' => 'API Test User',
            'email' => 'api@test.com',
            'password' => Hash::make('api123test'),
            'email_verified_at' => now(),
        ]);

        // Utilisateur non vérifié
        User::create([
            'name' => 'Utilisateur Non Vérifié',
            'email' => 'nonverifie@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null,
        ]);

        $this->command->info('✅ ' . User::count() . ' utilisateurs créés avec succès!');
        $this->command->info('');
        $this->command->info('📧 Comptes créés:');
        $this->command->info('   Admin: admin@ecommerce.com / admin123');
        $this->command->info('   Manager: manager@ecommerce.com / manager123');
        $this->command->info('   Vendeur: vendeur@ecommerce.com / vendeur123');
        $this->command->info('   Clients: *.example.com / password123');
        $this->command->info('   API Test: api@test.com / api123test');
    }
}
