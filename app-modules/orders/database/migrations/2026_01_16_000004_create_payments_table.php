<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            
            $table->string('transaction_id')->unique()->nullable();
            $table->string('payment_method'); // cash_on_delivery, stripe, paypal, etc.
            
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            
            $table->enum('status', [
                'pending',      // En attente
                'processing',   // En cours
                'completed',    // Complété
                'failed',       // Échoué
                'cancelled',    // Annulé
                'refunded'      // Remboursé
            ])->default('pending');
            
            // Détails du paiement (JSON pour flexibilité)
            $table->json('payment_details')->nullable();
            
            // Gateway response
            $table->text('gateway_response')->nullable();
            
            // Remboursement
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->timestamp('refunded_at')->nullable();
            $table->text('refund_reason')->nullable();
            
            // Timestamps de traitement
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            $table->timestamps();

            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
