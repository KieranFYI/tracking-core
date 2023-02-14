<?php

namespace KieranFYI\Tracking\Core\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use KieranFYI\Tracking\Core\Models\Tracking;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Models\TrackingRedirect;
use KieranFYI\Tracking\Core\Models\TrackingRequest;

class TrackingCorePackageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            'tracking' => Tracking::class,
            'trackingItem' => TrackingItem::class,
            'trackingRequest' => TrackingRequest::class,
            'trackingRedirect' => TrackingRedirect::class,
        ]);

        $root = realpath(__DIR__ . '/../..');

        $this->loadMigrationsFrom($root . '/database/migrations');
        $this->mergeConfigFrom($root . '/config/tracking.php', 'tracking');
        $this->loadRoutesFrom($root . '/routes/web.php');
    }
}
