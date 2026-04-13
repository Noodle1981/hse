<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;
use App\Models\DynamicInspection;
use App\Policies\InspectionPolicy;

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
        Gate::policy(\App\Models\DynamicInspection::class, \App\Policies\InspectionPolicy::class);
        Gate::policy(\App\Models\MasterReport::class, \App\Policies\MasterReportPolicy::class);
    }
}
