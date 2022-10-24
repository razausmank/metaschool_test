<?php

namespace App\Providers;

use App\Models\Quiz;
use App\Observers\GenericObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // model observers

        Quiz::observe(GenericObserver::class);
    }
}
