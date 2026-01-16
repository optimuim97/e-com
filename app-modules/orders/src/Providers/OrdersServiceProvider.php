<?php

namespace Modules\Orders\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Orders\Services\OrderService;
use Modules\Orders\Services\PaymentService;

class OrdersServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Enregistrer OrderService comme singleton
        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(PaymentService::class)
            );
        });

        // Enregistrer PaymentService comme singleton
        $this->app->singleton(PaymentService::class, function ($app) {
            return new PaymentService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Charger les migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Charger les routes
        $this->loadRoutesFrom(__DIR__ . '/../../routes/orders-routes.php');
    }
}
