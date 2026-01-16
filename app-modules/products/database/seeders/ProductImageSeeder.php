<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductImage;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            // Image principale
            ProductImage::create([
                'product_id' => $product->id,
                'path' => "products/{$product->slug}/main.jpg",
                'alt_text' => $product->name,
                'sort_order' => 0,
                'is_primary' => true,
            ]);

            // Images secondaires (2 à 4 selon le type de produit)
            $imageCount = rand(2, 4);
            
            for ($i = 1; $i <= $imageCount; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => "products/{$product->slug}/image-{$i}.jpg",
                    'alt_text' => "{$product->name} - Vue {$i}",
                    'sort_order' => $i,
                    'is_primary' => false,
                ]);
            }
        }

        $this->command->info('✅ ' . ProductImage::count() . ' images de produits créées!');
    }
}
