<?php

namespace Modules\Products\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Products\Models\Category;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductImage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les catégories
        $ordinateursPortables = Category::where('slug', 'ordinateurs-portables')->first();
        $iphone = Category::where('slug', 'iphone')->first();
        $android = Category::where('slug', 'android')->first();
        $tablettes = Category::where('slug', 'tablettes')->first();
        $casques = Category::where('slug', 'casques')->first();
        $ecouteurs = Category::where('slug', 'ecouteurs')->first();

        // === ORDINATEURS PORTABLES ===
        $macbookPro = Product::create([
            'name' => 'MacBook Pro 16" M3 Pro',
            'slug' => 'macbook-pro-16-m3-pro',
            'description' => 'Le MacBook Pro 16 pouces avec puce M3 Pro offre des performances exceptionnelles pour les professionnels créatifs. Écran Retina XDR, autonomie incroyable et puissance de calcul révolutionnaire.',
            'short_description' => 'Ordinateur portable professionnel ultime avec puce M3 Pro',
            'price' => 2999.99,
            'compare_price' => 3499.99,
            'cost' => 2200.00,
            'sku' => 'MBP-16-M3-512-GRAY',
            'barcode' => '194253123456',
            'quantity' => 25,
            'low_stock_threshold' => 5,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 2.15,
            'length' => 35.57,
            'width' => 24.81,
            'height' => 1.68,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'meta_title' => 'MacBook Pro 16" M3 Pro - Ordinateur Portable Professionnel',
            'meta_description' => 'Achetez le MacBook Pro 16 pouces avec puce M3 Pro. Performances exceptionnelles, écran Retina XDR.',
            'meta_keywords' => 'macbook pro, apple, m3 pro, ordinateur portable, professionnel',
            'attributes' => [
                'processor' => 'Apple M3 Pro 12-core',
                'ram' => '32GB',
                'storage' => '512GB SSD',
                'screen' => '16.2" Retina XDR',
                'gpu' => '18-core GPU',
                'color' => 'Space Gray',
                'battery' => '100Wh - jusqu\'à 22h',
                'ports' => '3x Thunderbolt 4, HDMI, SD Card',
            ],
            'published_at' => now()->subDays(10),
        ]);
        $macbookPro->categories()->attach([$ordinateursPortables->id]);

        $dellXps = Product::create([
            'name' => 'Dell XPS 15 9530',
            'slug' => 'dell-xps-15-9530',
            'description' => 'Le Dell XPS 15 combine élégance et performances. Écran InfinityEdge 4K OLED, processeur Intel Core i7 13e gen, parfait pour la création de contenu.',
            'short_description' => 'Ultrabook premium avec écran OLED 4K',
            'price' => 1899.99,
            'compare_price' => 2199.99,
            'cost' => 1400.00,
            'sku' => 'DELL-XPS-15-9530',
            'barcode' => '884116402350',
            'quantity' => 30,
            'low_stock_threshold' => 8,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 1.86,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'processor' => 'Intel Core i7-13700H',
                'ram' => '16GB DDR5',
                'storage' => '1TB NVMe SSD',
                'screen' => '15.6" 4K OLED Touch',
                'gpu' => 'NVIDIA RTX 4050 6GB',
                'color' => 'Platinum Silver',
            ],
            'published_at' => now()->subDays(15),
        ]);
        $dellXps->categories()->attach([$ordinateursPortables->id]);

        $lenovoThinkpad = Product::create([
            'name' => 'Lenovo ThinkPad X1 Carbon Gen 11',
            'slug' => 'lenovo-thinkpad-x1-carbon-gen-11',
            'description' => 'ThinkPad X1 Carbon légendaire pour les professionnels. Robuste, léger, et ultra-performant avec Intel vPro.',
            'short_description' => 'Ultraportable professionnel robuste et léger',
            'price' => 1699.99,
            'cost' => 1250.00,
            'sku' => 'LENOVO-X1C-G11',
            'barcode' => '196802123450',
            'quantity' => 40,
            'low_stock_threshold' => 10,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 1.12,
            'status' => 'active',
            'is_visible' => true,
            'attributes' => [
                'processor' => 'Intel Core i7-1355U vPro',
                'ram' => '16GB LPDDR5',
                'storage' => '512GB PCIe SSD',
                'screen' => '14" WUXGA IPS',
            ],
            'published_at' => now()->subDays(20),
        ]);
        $lenovoThinkpad->categories()->attach([$ordinateursPortables->id]);

        // === SMARTPHONES ===
        $iphone15Pro = Product::create([
            'name' => 'iPhone 15 Pro Max 256GB',
            'slug' => 'iphone-15-pro-max-256gb',
            'description' => 'iPhone 15 Pro Max avec puce A17 Pro, système de caméra révolutionnaire, et châssis en titane. Le smartphone le plus avancé jamais créé.',
            'short_description' => 'Le summum de l\'innovation Apple en titane',
            'price' => 1399.99,
            'compare_price' => 1499.99,
            'cost' => 950.00,
            'sku' => 'IPHONE-15-PM-256-TITAN',
            'barcode' => '195949123456',
            'quantity' => 150,
            'low_stock_threshold' => 20,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.221,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'meta_title' => 'iPhone 15 Pro Max 256GB - Titanium',
            'meta_description' => 'Nouveau iPhone 15 Pro Max avec puce A17 Pro et caméra 48MP',
            'attributes' => [
                'storage' => '256GB',
                'color' => 'Natural Titanium',
                'screen' => '6.7" Super Retina XDR',
                'chip' => 'A17 Pro',
                'camera' => '48MP Main + 12MP Ultra Wide + 12MP Telephoto',
                'battery' => 'Jusqu\'à 29h vidéo',
            ],
            'published_at' => now()->subDays(5),
        ]);
        $iphone15Pro->categories()->attach([$iphone->id]);

        $iphone15 = Product::create([
            'name' => 'iPhone 15 128GB',
            'slug' => 'iphone-15-128gb',
            'description' => 'iPhone 15 avec Dynamic Island, caméra 48MP, et USB-C. Design coloré et performances exceptionnelles.',
            'short_description' => 'iPhone 15 avec Dynamic Island et USB-C',
            'price' => 899.99,
            'cost' => 650.00,
            'sku' => 'IPHONE-15-128-BLUE',
            'barcode' => '195949234567',
            'quantity' => 200,
            'low_stock_threshold' => 30,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.171,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'storage' => '128GB',
                'color' => 'Blue',
                'screen' => '6.1" Super Retina XDR',
                'chip' => 'A16 Bionic',
            ],
            'published_at' => now()->subDays(8),
        ]);
        $iphone15->categories()->attach([$iphone->id]);

        $samsungS24Ultra = Product::create([
            'name' => 'Samsung Galaxy S24 Ultra 512GB',
            'slug' => 'samsung-galaxy-s24-ultra-512gb',
            'description' => 'Galaxy S24 Ultra avec Galaxy AI, caméra 200MP, et S Pen intégré. Le smartphone Android le plus puissant.',
            'short_description' => 'Flagship Android ultime avec AI et S Pen',
            'price' => 1399.99,
            'compare_price' => 1599.99,
            'cost' => 900.00,
            'sku' => 'SGS24U-512-GRAY',
            'barcode' => '8806095123456',
            'quantity' => 100,
            'low_stock_threshold' => 15,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.232,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'storage' => '512GB',
                'ram' => '12GB',
                'color' => 'Titanium Gray',
                'screen' => '6.8" Dynamic AMOLED 2X',
                'processor' => 'Snapdragon 8 Gen 3',
                'camera' => '200MP Main + AI Zoom',
                's_pen' => 'Inclus',
            ],
            'published_at' => now()->subDays(12),
        ]);
        $samsungS24Ultra->categories()->attach([$android->id]);

        $googlePixel8Pro = Product::create([
            'name' => 'Google Pixel 8 Pro 256GB',
            'slug' => 'google-pixel-8-pro-256gb',
            'description' => 'Pixel 8 Pro avec Google Tensor G3 et AI avancée. La meilleure photographie computationnelle du marché.',
            'short_description' => 'Excellence Google avec AI et photo computationnelle',
            'price' => 999.99,
            'cost' => 700.00,
            'sku' => 'PIXEL-8P-256-BAY',
            'barcode' => '840244700123',
            'quantity' => 80,
            'low_stock_threshold' => 12,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.213,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'storage' => '256GB',
                'ram' => '12GB',
                'color' => 'Bay Blue',
                'screen' => '6.7" LTPO OLED',
                'processor' => 'Google Tensor G3',
                'camera' => '50MP + AI Photo',
            ],
            'published_at' => now()->subDays(18),
        ]);
        $googlePixel8Pro->categories()->attach([$android->id]);

        // === TABLETTES ===
        $ipadPro = Product::create([
            'name' => 'iPad Pro 12.9" M2 256GB',
            'slug' => 'ipad-pro-129-m2-256gb',
            'description' => 'iPad Pro avec puce M2, écran Liquid Retina XDR, et compatibilité Apple Pencil 2. La tablette la plus puissante au monde.',
            'short_description' => 'Tablette professionnelle avec puce M2',
            'price' => 1299.99,
            'compare_price' => 1449.99,
            'cost' => 950.00,
            'sku' => 'IPAD-PRO-129-M2-256',
            'barcode' => '194253234567',
            'quantity' => 60,
            'low_stock_threshold' => 10,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.682,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'storage' => '256GB',
                'screen' => '12.9" Liquid Retina XDR',
                'chip' => 'Apple M2',
                'connectivity' => 'Wi-Fi 6E',
                'pencil' => 'Apple Pencil 2 compatible',
            ],
            'published_at' => now()->subDays(14),
        ]);
        $ipadPro->categories()->attach([$tablettes->id]);

        $samsungTabS9 = Product::create([
            'name' => 'Samsung Galaxy Tab S9 Ultra',
            'slug' => 'samsung-galaxy-tab-s9-ultra',
            'description' => 'Tab S9 Ultra avec écran AMOLED 14.6", S Pen inclus, et performances flagship. Parfaite pour la productivité.',
            'short_description' => 'Tablette Android premium géante',
            'price' => 1199.99,
            'cost' => 850.00,
            'sku' => 'TAB-S9U-256-GRAY',
            'barcode' => '8806094123456',
            'quantity' => 45,
            'low_stock_threshold' => 8,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.732,
            'status' => 'active',
            'is_visible' => true,
            'attributes' => [
                'storage' => '256GB',
                'ram' => '12GB',
                'screen' => '14.6" Dynamic AMOLED 2X',
                'processor' => 'Snapdragon 8 Gen 2',
                's_pen' => 'Inclus',
            ],
            'published_at' => now()->subDays(22),
        ]);
        $samsungTabS9->categories()->attach([$tablettes->id]);

        // === AUDIO ===
        $airpodsMax = Product::create([
            'name' => 'AirPods Max',
            'slug' => 'airpods-max',
            'description' => 'AirPods Max avec audio spatial et réduction de bruit active. Son haute-fidélité dans un design luxueux.',
            'short_description' => 'Casque premium Apple avec audio spatial',
            'price' => 579.99,
            'compare_price' => 629.99,
            'cost' => 400.00,
            'sku' => 'AIRPODS-MAX-SILVER',
            'barcode' => '194252123456',
            'quantity' => 75,
            'low_stock_threshold' => 15,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.384,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'color' => 'Silver',
                'type' => 'Over-ear',
                'anc' => 'Réduction de bruit active',
                'battery' => '20 heures',
                'spatial_audio' => 'Oui',
            ],
            'published_at' => now()->subDays(6),
        ]);
        $airpodsMax->categories()->attach([$casques->id]);

        $sonyWh1000xm5 = Product::create([
            'name' => 'Sony WH-1000XM5',
            'slug' => 'sony-wh-1000xm5',
            'description' => 'Casque sans fil premium avec la meilleure réduction de bruit du marché. Confort exceptionnel et son LDAC.',
            'short_description' => 'Meilleure réduction de bruit active',
            'price' => 399.99,
            'cost' => 250.00,
            'sku' => 'SONY-WH1000XM5-BLK',
            'barcode' => '4548736123456',
            'quantity' => 120,
            'low_stock_threshold' => 20,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.250,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'color' => 'Black',
                'type' => 'Over-ear',
                'anc' => 'Industry-leading ANC',
                'battery' => '30 heures',
                'codec' => 'LDAC, AAC, SBC',
            ],
            'published_at' => now()->subDays(9),
        ]);
        $sonyWh1000xm5->categories()->attach([$casques->id]);

        $airpodsPro2 = Product::create([
            'name' => 'AirPods Pro 2e génération',
            'slug' => 'airpods-pro-2',
            'description' => 'AirPods Pro 2 avec puce H2, réduction de bruit 2x meilleure, et boîtier USB-C avec Find My intégré.',
            'short_description' => 'Écouteurs sans fil avec ANC avancée',
            'price' => 249.99,
            'cost' => 170.00,
            'sku' => 'AIRPODS-PRO-2-USBC',
            'barcode' => '195949123450',
            'quantity' => 250,
            'low_stock_threshold' => 40,
            'track_inventory' => true,
            'stock_status' => 'in_stock',
            'weight' => 0.055,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'type' => 'In-ear',
                'anc' => 'Réduction de bruit adaptative',
                'battery' => '6h (30h avec boîtier)',
                'chip' => 'Apple H2',
                'usb_c' => 'Oui',
            ],
            'published_at' => now()->subDays(4),
        ]);
        $airpodsPro2->categories()->attach([$ecouteurs->id]);

        // === PRODUITS EN STOCK BAS ===
        $limitedProduct = Product::create([
            'name' => 'Édition Limitée - MacBook Air M2 Midnight',
            'slug' => 'macbook-air-m2-midnight-limited',
            'description' => 'Édition limitée du MacBook Air M2 en coloris Midnight exclusif.',
            'short_description' => 'Édition limitée - Stock limité',
            'price' => 1449.99,
            'cost' => 1050.00,
            'sku' => 'MBA-M2-MIDNIGHT-LTD',
            'barcode' => '194253345678',
            'quantity' => 3,
            'low_stock_threshold' => 5,
            'track_inventory' => true,
            'stock_status' => 'low_stock',
            'weight' => 1.24,
            'status' => 'active',
            'is_featured' => true,
            'is_visible' => true,
            'attributes' => [
                'storage' => '512GB',
                'ram' => '16GB',
                'color' => 'Midnight',
                'edition' => 'Limitée',
            ],
            'published_at' => now()->subDay(),
        ]);
        $limitedProduct->categories()->attach([$ordinateursPortables->id]);

        // === PRODUIT EN RUPTURE ===
        $outOfStock = Product::create([
            'name' => 'Samsung Galaxy Buds Pro 2',
            'slug' => 'samsung-galaxy-buds-pro-2',
            'description' => 'Écouteurs premium Samsung avec ANC 360°. Actuellement en rupture de stock.',
            'short_description' => 'Écouteurs flagship Samsung - Bientôt disponible',
            'price' => 229.99,
            'cost' => 150.00,
            'sku' => 'BUDS-PRO-2-WHT',
            'barcode' => '8806093123456',
            'quantity' => 0,
            'low_stock_threshold' => 10,
            'track_inventory' => true,
            'stock_status' => 'out_of_stock',
            'weight' => 0.043,
            'status' => 'active',
            'is_visible' => true,
            'attributes' => [
                'color' => 'White',
                'anc' => '360° ANC',
            ],
            'published_at' => now()->subDays(30),
        ]);
        $outOfStock->categories()->attach([$ecouteurs->id]);

        // === PRODUIT EN BROUILLON ===
        $draftProduct = Product::create([
            'name' => 'Nouveau Produit à Venir',
            'slug' => 'nouveau-produit-a-venir',
            'description' => 'Produit en préparation',
            'short_description' => 'Bientôt disponible',
            'price' => 999.99,
            'sku' => 'DRAFT-PRODUCT-001',
            'quantity' => 0,
            'status' => 'draft',
            'is_visible' => false,
        ]);

        $this->command->info('✅ ' . Product::count() . ' produits créés avec succès!');
    }
}
