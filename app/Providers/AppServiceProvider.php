<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\DetailHjual;
use App\Observers\DetailHjualObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        
        // Register observer untuk DetailHjual untuk handle batch inventory
        DetailHjual::observe(DetailHjualObserver::class);
    }
}
