<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;


class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    public const HOME = '/home';

    /**
     * Bootstrap services.
     */

    public function boot(): void
    {

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Route::middleware('api')
            //     ->prefix('api/san-pham')  // Có thể thêm tiền tố riêng cho các route này
            //     ->group(base_path('routes/san-pham/api.php'));
        });
    }
}
