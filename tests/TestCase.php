<?php

namespace KieranFYI\Tests\Tracking\Core;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use KieranFYI\Logging\Providers\LoggingPackageServiceProvider;
use KieranFYI\Misc\Providers\MiscPackageServiceProvider;
use KieranFYI\Services\Core\Providers\ServicesCorePackageServiceProvider;
use KieranFYI\Tracking\Core\Providers\TrackingCorePackageServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Load package service provider.
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MiscPackageServiceProvider::class,
            LoggingPackageServiceProvider::class,
            ServicesCorePackageServiceProvider::class,
            TrackingCorePackageServiceProvider::class,
        ];
    }
}