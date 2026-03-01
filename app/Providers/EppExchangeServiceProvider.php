<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class EppExchangeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Contracts\EppExchangeContract::class, function ($app) {
            $mode = config('epp_exchange.mode', 'mock');
            
            if ($mode === 'mock') {
                return new \App\Services\MockEppExchangeService();
            }

            // Aquí se instanciaría el ApiEppExchangeService en el futuro
            throw new \Exception("Modo de EPP Exchange no soportado: {$mode}");
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
