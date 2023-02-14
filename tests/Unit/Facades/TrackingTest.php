<?php

namespace Facades;

use KieranFYI\Tests\Tracking\Core\TestCase;
use KieranFYI\Tracking\Core\Facades\Tracking;
use KieranFYI\Tracking\Core\Services\TrackingService;

class TrackingTest extends TestCase
{

    public function testFacade()
    {
        $this->assertInstanceOf(TrackingService::class, Tracking::getFacadeRoot());
    }
}