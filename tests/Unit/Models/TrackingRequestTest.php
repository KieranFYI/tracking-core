<?php

namespace KieranFYI\Tests\Tracking\Core\Unit\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use KieranFYI\Misc\Traits\ImmutableTrait;
use KieranFYI\Services\Core\Traits\Serviceable;
use KieranFYI\Tests\Tracking\Core\Models\AuthModel;
use KieranFYI\Tests\Tracking\Core\TestCase;
use KieranFYI\Tracking\Core\Models\Tracking;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Models\TrackingRequest;

class TrackingRequestTest extends TestCase
{

    /**
     * @var TrackingRequest
     */
    private TrackingRequest $model;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TrackingRequest();
    }

    public function testIsModel()
    {
        $this->assertInstanceOf(TrackingRequest::class, $this->model);
    }

    public function testTraits()
    {
        $uses = class_uses_recursive(Tracking::class);
        $this->assertContains(Serviceable::class, $uses);
        $this->assertContains(ImmutableTrait::class, $uses);
    }

    public function testFillable()
    {
        $this->assertEquals([
            'ip', 'ips', 'trusted_proxy', 'user_agent'
        ], $this->model->getFillable());
    }

    public function testCasts()
    {
        $this->assertEquals([
            'ips' => 'array',
            'trusted_proxy' => 'boolean',
            'id' => 'int',
        ], $this->model->getCasts());
    }

    public function testTracking()
    {
        $this->artisan('migrate');

        $this->assertInstanceOf(BelongsTo::class, $this->model->tracking());
        $tracking = new Tracking();
        $this->model->tracking()->associate($tracking);
        $this->assertTrue($tracking->is($this->model->tracking));
    }

    public function testItem()
    {
        $this->artisan('migrate');

        $this->assertInstanceOf(BelongsTo::class, $this->model->item());
        $item = new TrackingItem();
        $this->model->item()->associate($item);
        $this->assertTrue($item->is($this->model->item));
    }

    public function testUser()
    {
        $this->artisan('migrate');

        $this->assertInstanceOf(MorphTo::class, $this->model->user());
        $testModel = new AuthModel();
        $this->model->user()->associate($testModel);
        $this->assertTrue($testModel->is($this->model->user));
    }
}