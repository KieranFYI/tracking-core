<?php

namespace KieranFYI\Tests\Tracking\Core\Unit\Services;

use Illuminate\Support\Facades\Schema;
use KieranFYI\Tests\Tracking\Core\Models\AuthModel;
use KieranFYI\Tests\Tracking\Core\Models\TestModel;
use KieranFYI\Tests\Tracking\Core\TestCase;
use KieranFYI\Tracking\Core\Models\Tracking;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Models\TrackingRedirect;
use KieranFYI\Tracking\Core\Services\TrackingService;
use TypeError;

class TrackingServiceTest extends TestCase
{

    /**
     * @var TrackingService
     */
    private TrackingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TrackingService::class);

        Schema::create('test_models', function ($table) {
            $table->temporary();
            $table->id();
            $table->timestamps();
        });
    }

    public function testAsParent()
    {
        $this->artisan('migrate');
        $parent = TestModel::create([]);
        $this->service->asParent($parent, function () use ($parent) {
            $this->assertTrue($parent->is($this->service->tracking()->parent));
        });
        $this->assertNull($this->service->tracking());
    }

    public function testAsParentExisting()
    {
        $this->artisan('migrate');
        $parent = TestModel::create([]);
        $tracking = new Tracking();
        $tracking->parent()->associate($parent);
        $tracking->save();

        $this->service->asParent($parent, function () use ($tracking) {
            $this->assertTrue($tracking->is($this->service->tracking()));
        });
    }

    public function testTrack()
    {
        $this->artisan('migrate');

        $parent = TestModel::create([]);
        $this->service->asParent($parent, function () {
            $trackable = TrackingRedirect::create(['redirect' => 'https://www.example.com/']);
            $item = $this->service->track($trackable);
            $this->assertInstanceOf(TrackingItem::class, $item);
        });
    }

    public function testTrackNullParent()
    {
        $trackable = new TestModel();
        $this->expectException(TypeError::class);
        $this->service->track($trackable);
    }

    public function testTrackInvalidTrackingInterface()
    {
        $this->artisan('migrate');

        $parent = TestModel::create([]);
        $this->service->asParent($parent, function () {
            $trackable = new TestModel();
            $this->expectException(TypeError::class);
            $this->service->track($trackable);
        });
    }

    public function testTrackUserNotNull()
    {
        Schema::create('auth_models', function ($table) {
            $table->temporary();
            $table->id();
            $table->timestamps();
        });
        $this->artisan('migrate');

        $parent = TestModel::create([]);
        $this->service->asParent($parent, function () {
            $user = AuthModel::create([]);
            $trackable = TrackingRedirect::create(['redirect' => 'https://www.example.com/']);
            $item = $this->service->track($trackable, $user);
            $this->assertInstanceOf(TrackingItem::class, $item);
            $this->assertTrue($user->is($item->user));
        });
    }

    public function testTrackCreatedByNotNull()
    {
        $this->markTestIncomplete();
    }

    public function testTrackExistingItem()
    {
        $this->markTestIncomplete();
    }

    public function testRedirect()
    {
        $this->artisan('migrate');
        $parent = TestModel::create([]);
        $this->service->asParent($parent, function () {
            $item = $this->service->redirect('https://www.example.com/');
            $this->assertInstanceOf(TrackingItem::class, $item);
        });
    }

    public function testRedirectNullParent()
    {
        $this->expectException(TypeError::class);
        $this->service->redirect('https://www.example.com/');
    }

    public function testRedirectUserNotNull()
    {
        Schema::create('auth_models', function ($table) {
            $table->temporary();
            $table->id();
            $table->timestamps();
        });
        $this->artisan('migrate');

        $parent = TestModel::create([]);
        $this->service->asParent($parent, function () {
            $user = AuthModel::create([]);
            $item = $this->service->redirect('https://www.example.com/', $user);
            $this->assertInstanceOf(TrackingItem::class, $item);
            $this->assertTrue($user->is($item->user));
        });
    }

    public function testRedirectExistingItem()
    {
        $this->artisan('migrate');
        $parent = TestModel::create([]);
        $this->service->asParent($parent, function () {
            $item = $this->service->redirect('https://www.example.com/');
            $this->assertInstanceOf(TrackingItem::class, $item);
            $response = $this->service->redirect('https://www.example.com/');
            $this->assertTrue($item->is($response));
        });
    }

}