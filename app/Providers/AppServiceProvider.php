<?php

namespace App\Providers;

use App\Events\ClientAccessModified;
use App\Events\InspectionCompleted;
use App\Listeners\LogClientAccess;
use App\Listeners\SyncAssetMetadata;
use App\Models\Asset;
use App\Models\DynamicInspection;
use App\Models\MasterReport;
use App\Observers\AssetObserver;
use App\Policies\InspectionPolicy;
use App\Policies\MasterReportPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(DynamicInspection::class, InspectionPolicy::class);
        Gate::policy(MasterReport::class, MasterReportPolicy::class);

        Event::listen(
            ClientAccessModified::class,
            LogClientAccess::class
        );

        Event::listen(
            InspectionCompleted::class,
            SyncAssetMetadata::class
        );

        Asset::observe(AssetObserver::class);
    }
}
