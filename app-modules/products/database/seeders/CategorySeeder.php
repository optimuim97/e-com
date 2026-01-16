<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Products\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Électronique
        $electronique = Category::create([
            'name' => 'Électronique',
            'slug' => 'electronique',
            'description' => 'Tous les produits électroniques et high-tech',
            'is_active' => true,
            'sort_order' => 0,
            'meta_title' => 'Électronique - Boutique E-Commerce',
            'meta_description' => 'Découvrez notre large gamme de produits électroniques',
            'meta_keywords' => 'électronique, high-tech, gadgets',
        ]);

        // Sous-catégories Électronique
        $ordinateurs = Category::create([
            'name' => 'Ordinateurs',
            'slug' => 'ordinateurs',
            'description' => 'Ordinateurs portables et de bureau',
            'parent_id' => $electronique->id,
            'is_active' => true,
            'sort_order' => 0,
            'meta_title' => 'Ordinateurs - Portable & Bureau',
            'meta_description' => 'Ordinateurs portables, PC de bureau, workstations',
        ]);

        Category::create([
            'name' => 'Ordinateurs Portables',
            'slug' => 'ordinateurs-portables',
            'description' => 'Laptops pour tous les usages',
            'parent_id' => $ordinateurs->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Category::create([
            'name' => 'PC de Bureau',
            'slug' => 'pc-de-bureau',
            'description' => 'Ordinateurs de bureau puissants',
            'parent_id' => $ordinateurs->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $smartphones = Category::create([
            'name' => 'Smartphones',
            'slug' => 'smartphones',
            'description' => 'Les derniers smartphones du marché',
            'parent_id' => $electronique->id,
            'is_active' => true,
            'sort_order' => 1,
            'meta_title' => 'Smartphones - Derniers Modèles',
            'meta_description' => 'iPhone, Samsung, Google Pixel et plus',
        ]);

        Category::create([
            'name' => 'iPhone',
            'slug' => 'iphone',
            'description' => 'Toute la gamme iPhone',
            'parent_id' => $smartphones->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Category::create([
            'name' => 'Android',
            'slug' => 'android',
            'description' => 'Smartphones Android',
            'parent_id' => $smartphones->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $tablettes = Category::create([
            'name' => 'Tablettes',
            'slug' => 'tablettes',
            'description' => 'Tablettes tactiles et accessoires',
            'parent_id' => $electronique->id,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $audio = Category::create([
            'name' => 'Audio',
            'slug' => 'audio',
            'description' => 'Casques, écouteurs et systèmes audio',
            'parent_id' => $electronique->id,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Category::create([
            'name' => 'Casques',
            'slug' => 'casques',
            'description' => 'Casques audio filaires et sans fil',
            'parent_id' => $audio->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Category::create([
            'name' => 'Écouteurs',
            'slug' => 'ecouteurs',
            'description' => 'Écouteurs intra-auriculaires',
            'parent_id' => $audio->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Maison & Jardin
        $maison = Category::create([
            'name' => 'Maison & Jardin',
            'slug' => 'maison-jardin',
            'description' => 'Tout pour la maison et le jardin',
            'is_active' => true,
            'sort_order' => 1,
            'meta_title' => 'Maison & Jardin - Décoration & Équipement',
        ]);

        Category::create([
            'name' => 'Électroménager',
            'slug' => 'electromenager',
            'description' => 'Appareils électroménagers',
            'parent_id' => $maison->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Category::create([
            'name' => 'Meubles',
            'slug' => 'meubles',
            'description' => 'Meubles pour toutes les pièces',
            'parent_id' => $maison->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Mode & Accessoires
        $mode = Category::create([
            'name' => 'Mode & Accessoires',
            'slug' => 'mode-accessoires',
            'description' => 'Vêtements et accessoires de mode',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        Category::create([
            'name' => 'Vêtements Homme',
            'slug' => 'vetements-homme',
            'description' => 'Mode masculine',
            'parent_id' => $mode->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Category::create([
            'name' => 'Vêtements Femme',
            'slug' => 'vetements-femme',
            'description' => 'Mode féminine',
            'parent_id' => $mode->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Sport & Loisirs
        $sport = Category::create([
            'name' => 'Sport & Loisirs',
            'slug' => 'sport-loisirs',
            'description' => 'Articles de sport et loisirs',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        Category::create([
            'name' => 'Fitness',
            'slug' => 'fitness',
            'description' => 'Équipements de fitness',
            'parent_id' => $sport->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        Category::create([
            'name' => 'Sports Outdoor',
            'slug' => 'sports-outdoor',
            'description' => 'Sports de plein air',
            'parent_id' => $sport->id,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->command->info('✅ Catégories créées avec succès!');
    }
}
