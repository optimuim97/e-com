<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Products\Models\Product;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier qu'on a des utilisateurs et des produits
        $users = User::limit(3)->get();
        $products = Product::where('stock_status', 'in_stock')->limit(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn('Aucun utilisateur trouvé. Créez des utilisateurs d\'abord.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command->warn('Aucun produit trouvé. Créez des produits d\'abord.');
            return;
        }

        $this->command->info('Création de paniers de test...');

        // Créer 3 paniers pour utilisateurs connectés
        foreach ($users as $index => $user) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'session_id' => null,
                'expires_at' => null,
            ]);

            // Ajouter 2-5 articles au panier
            $itemCount = rand(2, 5);
            $selectedProducts = $products->random(min($itemCount, $products->count()));

            foreach ($selectedProducts as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                    'options' => rand(0, 1) ? [
                        'color' => ['red', 'blue', 'green', 'black'][rand(0, 3)],
                        'size' => ['S', 'M', 'L', 'XL'][rand(0, 3)],
                    ] : null,
                ]);
            }

            // Mettre à jour les totaux du panier
            $cart->updateTotals();

            // Appliquer un coupon aléatoirement
            if (rand(0, 1)) {
                $cart->update([
                    'coupon_code' => 'PROMO10',
                    'discount' => $cart->subtotal * 0.10,
                ]);
                $cart->updateTotals();
            }

            $this->command->info("✓ Panier créé pour {$user->name} avec {$cart->items->count()} articles");
        }

        // Créer 2 paniers invités (session-based)
        for ($i = 1; $i <= 2; $i++) {
            $sessionId = \Illuminate\Support\Str::uuid()->toString();
            
            $cart = Cart::create([
                'user_id' => null,
                'session_id' => $sessionId,
                'expires_at' => now()->addDays(7),
            ]);

            // Ajouter quelques articles
            $itemCount = rand(1, 3);
            $selectedProducts = $products->random(min($itemCount, $products->count()));

            foreach ($selectedProducts as $product) {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 2),
                    'price' => $product->price,
                    'options' => null,
                ]);
            }

            $cart->updateTotals();

            $this->command->info("✓ Panier invité créé (session: {$sessionId}) avec {$cart->items->count()} articles");
        }

        $this->command->info('✅ Seeders Cart terminés !');
    }
}
