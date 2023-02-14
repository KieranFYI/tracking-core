<?php

namespace KieranFYI\Tests\Tracking\Core\Unit\Services;

use Illuminate\Support\Facades\Schema;
use KieranFYI\Tests\Tracking\Core\Models\AuthModel;
use KieranFYI\Tests\Tracking\Core\Models\TestModel;
use KieranFYI\Tests\Tracking\Core\TestCase;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Models\Tracking;
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

    public function testParent()
    {
        $this->artisan('migrate');
        $parent = TestModel::create([]);
        $this->service
            ->parent($parent);
        $this->assertTrue($parent->is($this->service->tracking()->parent));
    }

    public function testParentExisting()
    {
        $this->artisan('migrate');
        $parent = TestModel::create([]);
        $tracking = new Tracking();
        $tracking->parent()
            ->associate($parent);
        $tracking->save();

        $this->service
            ->parent($parent)
            ->group(function () use ($tracking) {
                $this->assertTrue($tracking->is($this->service->tracking()));
            });
    }

    public function testTrack()
    {
        $this->artisan('migrate');

        $parent = TestModel::create([]);
        $this->service
            ->parent($parent)
            ->group(function () {
                $trackable = TrackingRedirect::create(['redirect' => 'https://www.example.com/']);
                $item = $this->service->track($trackable);
                $this->assertInstanceOf(TrackingItem::class, $item);
            });
    }

    public function testTrackNullParent()
    {
        $trackable = new TestModel();
        $this->expectException(TypeError::class);
        $this->service
            ->track($trackable);
    }

    public function testTrackInvalidTrackingInterface()
    {
        $this->artisan('migrate');

        $parent = TestModel::create([]);
        $this->service
            ->parent($parent)
            ->group(function () {
                $trackable = new TestModel();
                $this->expectException(TypeError::class);
                $this->service
                    ->track($trackable);
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
        $user = AuthModel::create([]);
        $this->service
            ->parent($parent)
            ->user($user)
            ->group(function () use ($user) {
                $trackable = TrackingRedirect::create(['redirect' => 'https://www.example.com/']);
                $item = $this->service
                    ->track($trackable);
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
        $this->service
            ->parent($parent)
            ->group(function () {
                $item = $this->service
                    ->redirect('https://www.example.com/');
                $this->assertInstanceOf(TrackingItem::class, $item);
            });
    }

    public function testRedirectNullParent()
    {
        $this->expectException(TypeError::class);
        $this->service
            ->redirect('https://www.example.com/');
    }

    public function testRedirectUserNotNull()
    {
        Schema::create('auth_models', function ($table) {
            $table->temporary();
            $table->id();
            $table->timestamps();
        });
        $this->artisan('migrate');

        $user = AuthModel::create([]);
        $parent = TestModel::create([]);
        $this->service
            ->user($user)
            ->parent($parent)
            ->group(function () use ($user) {
                $item = $this->service->redirect('https://www.example.com/');
                $this->assertInstanceOf(TrackingItem::class, $item);
                $this->assertTrue($user->is($item->user));
            });
    }

    public function testRedirectExistingItem()
    {
        $this->artisan('migrate');
        $parent = TestModel::create([]);
        $this->service
            ->parent($parent)
            ->group(function () {
                $item = $this->service->redirect('https://www.example.com/');
                $this->assertInstanceOf(TrackingItem::class, $item);
                $response = $this->service->redirect('https://www.example.com/');
                $this->assertTrue($item->is($response));
            });
    }

}