<?php

namespace App\Providers;

use sdks\github\Github;
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
        $this->app->singleton(Github::class, function(){
            $scopes = [
                "notifications",
                "read:user",
            ];
            return new Github(config('services.github.client_id'), config('services.github.client_secret'), config('services.github.redirect_uri'), $scopes);
        });
    }
}
