<?php

return [
    App\Providers\AppServiceProvider::class,
    Modules\Auth\Providers\AuthServiceProvider::class,
    Modules\Products\Providers\ProductsServiceProvider::class,
    Modules\Cart\Providers\CartServiceProvider::class,
    Modules\Orders\Providers\OrdersServiceProvider::class,
];
