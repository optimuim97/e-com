<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Montants
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Statut de la commande
            $table->enum('status', [
                'pending',           // En attente
                'confirmed',         // Confirmée
                'processing',        // En traitement
                'shipped',          // Expédiée
                'delivered',        // Livrée
                'cancelled',        // Annulée
                'refunded'          // Remboursée
            ])->default('pending');
            
            // Statut de paiement
            $table->enum('payment_status', [
                'pending',          // En attente
                'paid',             // Payé
                'partially_paid',   // Partiellement payé
                'failed',           // Échoué
                'refunded'          // Remboursé
            ])->default('pending');
            
            // Méthode de paiement
            $table->enum('payment_method', [
                'cash_on_delivery', // Paiement à la livraison
                'online',           // Paiement en ligne
                'partial'           // Paiement partiel
            ]);
            
            // Montants payés
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2)->default(0);
            
            // Coupon
            $table->string('coupon_code')->nullable();
            
            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Tracking
            $table->string('tracking_number')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('order_number');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
