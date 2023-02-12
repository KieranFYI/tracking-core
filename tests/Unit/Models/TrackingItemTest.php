<?php

namespace KieranFYI\Tests\Tracking\Core\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;
use KieranFYI\Logging\Traits\LoggingTrait;
use KieranFYI\Misc\Traits\HashTrait;
use KieranFYI\Misc\Traits\ImmutableTrait;
use KieranFYI\Services\Core\Traits\Serviceable;
use KieranFYI\Tests\Tracking\Core\Models\AuthModel;
use KieranFYI\Tests\Tracking\Core\Models\TestModel;
use KieranFYI\Tests\Tracking\Core\TestCase;
use KieranFYI\Tracking\Core\Models\Tracking;
use KieranFYI\Tracking\Core\Models\TrackingItem;

class TrackingItemTest extends TestCase
{

    /**
     * @var TrackingItem
     */
    private TrackingItem $model;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new TrackingItem();
    }

    public function testIsModel()
    {
        $this->assertInstanceOf(Model::class, $this->model);
    }

    public function testTraits()
    {
        $uses = class_uses_recursive(TrackingItem::class);
        $this->assertContains(SoftDeletes::class, $uses);
        $this->assertContains(LoggingTrait::class, $uses);
        $this->assertContains(Serviceable::class, $uses);
        $this->assertContains(ImmutableTrait::class, $uses);
        $this->assertContains(HashTrait::class, $uses);
    }

    public function testTracking()
    {
        $this->artisan('migrate');

        $this->assertInstanceOf(BelongsTo::class, $this->model->tracking());
        $tracking = new Tracking();
        $this->model->tracking()->associate($tracking);
        $this->assertTrue($tracking->is($this->model->tracking));
    }

    public function testUser()
    {
        $this->artisan('migrate');

        $this->assertInstanceOf(MorphTo::class, $this->model->user());
        $testModel = new AuthModel();
        $this->model->user()->associate($testModel);
        $this->assertTrue($testModel->is($this->model->user));
    }

    public function testCreatedBy()
    {
        $this->artisan('migrate');

        $this->assertInstanceOf(MorphTo::class, $this->model->createdBy());
        $testModel = new AuthModel();
        $this->model->createdBy()->associate($testModel);
        $this->assertTrue($testModel->is($this->model->createdBy));
    }

    public function testTrackable()
    {
        $this->artisan('migrate');

        $this->assertInstanceOf(MorphTo::class, $this->model->trackable());
        $testModel = new TestModel();
        $this->model->trackable()->associate($testModel);
        $this->assertTrue($testModel->is($this->model->trackable));
    }

    public function testGetUrlAttribute()
    {
        $this->model->id = 1;
        $this->model->hash = 'test';

        $this->assertEquals('http://localhost/l/1/test', $this->model->url);
    }
}