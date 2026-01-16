<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Démarrage du seeding de la base de données...');
        $this->command->info('');

        // Utilisateurs
        $this->command->info('👥 Création des utilisateurs...');
        $this->call(UserSeeder::class);
        $this->command->info('');

        // Catégories (doit être avant les produits)
        $this->command->info('📁 Création des catégories...');
        $this->call(\Modules\Products\Database\Seeders\CategorySeeder::class);
        $this->command->info('');

        // Produits
        $this->command->info('📦 Création des produits...');
        $this->call(\Modules\Products\Database\Seeders\ProductSeeder::class);
        $this->command->info('');

        // Images des produits
        $this->command->info('🖼️  Création des images de produits...');
        $this->call(\Modules\Products\Database\Seeders\ProductImageSeeder::class);
        $this->command->info('');

        $this->command->info('✅ Seeding terminé avec succès!');
        $this->command->info('');
        $this->command->info('📊 Statistiques:');
        $this->command->info('   Utilisateurs: ' . \App\Models\User::count());
        $this->command->info('   Catégories: ' . \Modules\Products\Models\Category::count());
        $this->command->info('   Produits: ' . \Modules\Products\Models\Product::count());
        $this->command->info('   Images: ' . \Modules\Products\Models\ProductImage::count());
    }
}
