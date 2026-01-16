<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Orders\Models\Order;
use Modules\Orders\Models\OrderItem;
use Modules\Orders\Models\Payment;
use Modules\Orders\Models\ShippingAddress;
use Modules\Products\Models\Product;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::limit(3)->get();
        $products = Product::limit(10)->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('Besoin d\'utilisateurs et de produits pour créer des commandes.');
            return;
        }

        $this->command->info('Création de commandes de test...');

        $statuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentMethods = ['cash_on_delivery', 'online', 'partial'];
        $cities = ['Paris', 'Lyon', 'Marseille', 'Toulouse', 'Nice', 'Nantes', 'Bordeaux'];

        foreach ($users as $user) {
            // Créer 2-4 commandes par utilisateur
            $orderCount = rand(2, 4);

            for ($i = 0; $i < $orderCount; $i++) {
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                $status = $statuses[array_rand($statuses)];

                // Calculer les montants
                $itemsCount = rand(1, 4);
                $selectedProducts = $products->random(min($itemsCount, $products->count()));
                
                $subtotal = 0;
                $orderItems = [];

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 3);
                    $price = $product->price;
                    $itemSubtotal = $price * $quantity;
                    $subtotal += $itemSubtotal;

                    $orderItems[] = [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku ?? 'SKU-' . $product->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $itemSubtotal,
                        'options' => null,
                    ];
                }

                $tax = $subtotal * 0.20; // 20% TVA
                $shippingCost = $subtotal >= 100 ? 0 : 5.99;
                $discount = rand(0, 1) ? $subtotal * 0.10 : 0;
                $total = $subtotal + $tax + $shippingCost - $discount;

                // Déterminer paid_amount selon le payment_method
                $paidAmount = match ($paymentMethod) {
                    'online' => $total, // Payé en entier
                    'partial' => $total * 0.5, // 50% payé
                    'cash_on_delivery' => $status === 'delivered' ? $total : 0, // Payé si livré
                };

                $remainingAmount = $total - $paidAmount;

                // Déterminer payment_status
                $paymentStatus = match (true) {
                    $paidAmount == 0 => 'pending',
                    $paidAmount >= $total => 'paid',
                    $paidAmount > 0 => 'partially_paid',
                    default => 'pending',
                };

                // Créer la commande
                $order = Order::create([
                    'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'user_id' => $user->id,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $paymentMethod,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping_cost' => $shippingCost,
                    'discount' => $discount,
                    'total' => $total,
                    'paid_amount' => $paidAmount,
                    'remaining_amount' => $remainingAmount,
                    'coupon_code' => $discount > 0 ? 'PROMO10' : null,
                    'customer_notes' => rand(0, 1) ? 'Livraison entre 9h et 17h SVP' : null,
                    'admin_notes' => $status === 'cancelled' ? 'Annulé par le client' : null,
                    'tracking_number' => in_array($status, ['shipped', 'delivered']) ? 'TRACK' . strtoupper(\Illuminate\Support\Str::random(10)) : null,
                    'confirmed_at' => in_array($status, ['confirmed', 'processing', 'shipped', 'delivered']) ? now()->subDays(rand(1, 5)) : null,
                    'shipped_at' => in_array($status, ['shipped', 'delivered']) ? now()->subDays(rand(1, 3)) : null,
                    'delivered_at' => $status === 'delivered' ? now()->subDays(rand(0, 2)) : null,
                    'cancelled_at' => $status === 'cancelled' ? now()->subDays(rand(0, 1)) : null,
                ]);

                // Créer les items
                foreach ($orderItems as $itemData) {
                    OrderItem::create(array_merge($itemData, ['order_id' => $order->id]));
                }

                // Créer l'adresse de livraison
                $city = $cities[array_rand($cities)];
                ShippingAddress::create([
                    'order_id' => $order->id,
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'phone' => '06' . rand(10000000, 99999999),
                    'company' => rand(0, 1) ? 'Ma Société SARL' : null,
                    'address_line1' => rand(1, 999) . ' Rue de ' . ['la Paix', 'la République', 'Victor Hugo'][rand(0, 2)],
                    'address_line2' => rand(0, 1) ? 'Appartement ' . rand(1, 50) : null,
                    'city' => $city,
                    'state' => ['Île-de-France', 'Auvergne-Rhône-Alpes', 'Provence-Alpes-Côte d\'Azur'][rand(0, 2)],
                    'postal_code' => rand(10000, 99999),
                    'country' => 'FR',
                    'delivery_instructions' => rand(0, 1) ? 'Sonner deux fois' : null,
                    'address_type' => ['home', 'office', 'other'][rand(0, 2)],
                ]);

                // Créer les paiements
                if ($paidAmount > 0) {
                    // Premier paiement
                    $firstPaymentAmount = $paymentMethod === 'partial' ? $total * 0.3 : $paidAmount;
                    
                    Payment::create([
                        'order_id' => $order->id,
                        'transaction_id' => 'TXN-' . uniqid() . '-' . time(),
                        'payment_method' => $paymentMethod === 'cash_on_delivery' ? 'cash_on_delivery' : 'stripe',
                        'amount' => $firstPaymentAmount,
                        'currency' => 'EUR',
                        'status' => 'completed',
                        'payment_details' => $paymentMethod !== 'cash_on_delivery' ? json_encode(['card_last4' => '4242', 'card_brand' => 'visa']) : null,
                        'gateway_response' => 'Success',
                        'processed_at' => now()->subDays(rand(1, 5)),
                    ]);

                    // Deuxième paiement si partiel
                    if ($paymentMethod === 'partial' && $paidAmount > $firstPaymentAmount) {
                        Payment::create([
                            'order_id' => $order->id,
                            'transaction_id' => 'TXN-' . uniqid() . '-' . time(),
                            'payment_method' => 'stripe',
                            'amount' => $paidAmount - $firstPaymentAmount,
                            'currency' => 'EUR',
                            'status' => 'completed',
                            'payment_details' => json_encode(['card_last4' => '4242', 'card_brand' => 'visa']),
                            'gateway_response' => 'Success',
                            'processed_at' => now()->subDays(rand(0, 3)),
                        ]);
                    }
                }

                $this->command->info("✓ Commande {$order->order_number} créée pour {$user->name} - Status: {$status} - Payment: {$paymentMethod}");
            }
        }

        // Créer une commande avec remboursement
        $user = $users->first();
        $products = Product::limit(2)->get();
        
        if ($products->count() >= 2) {
            $subtotal = $products->sum('price') * 2;
            $total = $subtotal * 1.20; // avec taxe
            
            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
                'user_id' => $user->id,
                'status' => 'refunded',
                'payment_status' => 'refunded',
                'payment_method' => 'online',
                'subtotal' => $subtotal,
                'tax' => $subtotal * 0.20,
                'shipping_cost' => 0,
                'discount' => 0,
                'total' => $total,
                'paid_amount' => 0, // Remboursé
                'remaining_amount' => $total,
                'confirmed_at' => now()->subDays(10),
                'cancelled_at' => now()->subDays(2),
            ]);

            foreach ($products as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku ?? 'SKU-' . $product->id,
                    'quantity' => 2,
                    'price' => $product->price,
                    'subtotal' => $product->price * 2,
                ]);
            }

            ShippingAddress::create([
                'order_id' => $order->id,
                'full_name' => $user->name,
                'email' => $user->email,
                'phone' => '0612345678',
                'address_line1' => '123 Rue Test',
                'city' => 'Paris',
                'postal_code' => '75001',
                'country' => 'FR',
                'address_type' => 'home',
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => 'TXN-' . uniqid() . '-' . time(),
                'payment_method' => 'stripe',
                'amount' => $total,
                'currency' => 'EUR',
                'status' => 'refunded',
                'refunded_amount' => $total,
                'refund_reason' => 'Produit défectueux',
                'refunded_at' => now()->subDays(2),
                'processed_at' => now()->subDays(10),
            ]);

            $this->command->info("✓ Commande remboursée créée: {$order->order_number}");
        }

        $this->command->info('✅ Seeders Orders terminés !');
    }
}
