<?php

namespace KieranFYI\Tests\Tracking\Core\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use KieranFYI\Logging\Traits\LoggingTrait;
use KieranFYI\Services\Core\Traits\Serviceable;
use KieranFYI\Tests\Tracking\Core\TestCase;
use KieranFYI\Tracking\Core\Interfaces\TrackingInterface;
use KieranFYI\Tracking\Core\Models\Tracking;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Models\TrackingRedirect;

class TrackingRedirectTest extends TestCase
{

    /**
     * @var TrackingRedirect
     */
    private TrackingRedirect $model;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TrackingRedirect();
    }

    public function testExtends()
    {
        $this->assertInstanceOf(Model::class, $this->model);
    }

    public function testImplements()
    {
        $this->assertInstanceOf(TrackingInterface::class, $this->model);
    }

    public function testTraits()
    {
        $uses = class_uses_recursive(Tracking::class);
        $this->assertContains(SoftDeletes::class, $uses);
        $this->assertContains(LoggingTrait::class, $uses);
        $this->assertContains(Serviceable::class, $uses);
    }

    public function testFillable()
    {
        $this->assertEquals(['redirect'], $this->model->getFillable());
    }

    public function testBuildResponse()
    {
        $targetUrl = 'https://www.example.com/';
        $this->model->redirect = $targetUrl;
        $response = $this->model->buildResponse(new TrackingItem());
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($targetUrl, $response->getTargetUrl());
    }
}