<?php

namespace KieranFYI\Tracking\Core\Facades;

use Illuminate\Support\Facades\Facade;
use KieranFYI\Tracking\Core\Services\TrackingService;

class Tracking extends Facade
{
    /**
     * @var bool
     */
    protected static $cached = true;

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TrackingService::class;
    }
}