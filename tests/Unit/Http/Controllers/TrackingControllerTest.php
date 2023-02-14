<?php

namespace KieranFYI\Tests\Tracking\Core\Unit\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;
use KieranFYI\Tests\Tracking\Core\Models\BadTestModel;
use KieranFYI\Tests\Tracking\Core\Models\TestModel;
use KieranFYI\Tests\Tracking\Core\TestCase;
use KieranFYI\Tracking\Core\Facades\Tracking as TrackingFacade;
use KieranFYI\Tracking\Core\Models\Tracking as TrackingModel;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Models\TrackingRedirect;

class TrackingControllerTest extends TestCase
{

    /**
     * @var TestModel
     */
    private TestModel $parent;

    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('test_models', function ($table) {
            $table->temporary();
            $table->id();
            $table->timestamps();
        });
        $this->artisan('migrate');

        $this->parent = TestModel::create([]);
    }

    public function testShow()
    {
        /** @var TrackingItem $item */
        $item = null;
        /** @var TrackingModel $tracking */
        $tracking = null;
        TrackingFacade::parent($this->parent)->group(function () use (&$item, &$tracking) {
            $tracking = TrackingFacade::tracking();
            $trackable = TrackingRedirect::create(['redirect' => 'https://www.example.com/']);
            $item = TrackingFacade::track($trackable);
        });

        $this->assertNotNull($item);
        $this->get($item->url)
            ->assertRedirect('https://www.example.com/');

        $this->assertNotNull($tracking);
        $request = $tracking->requests->first();
        $this->assertNotNull($request);

    }

    public function testShowInvalidKey()
    {
        /** @var TrackingItem $item */
        $item = null;
        TrackingFacade::parent($this->parent)
            ->group(function () use (&$item) {
                $trackable = TrackingRedirect::create(['redirect' => 'https://www.example.com/']);
                $item = TrackingFacade::track($trackable);
            });

        $this->assertNotNull($item);
        $this->get(
            route('tracking', [
                'key' => 'test',
                'item' => $item->getRouteKey(),
            ])
        )
            ->assertStatus(400);
    }

    public function testShowUserNotNull()
    {
        $this->markTestIncomplete();
    }

    public function testShowUserNotNullInvalidUser()
    {
        Schema::create('users', function ($table) {
            $table->temporary();
            $table->id();
            $table->timestamps();
        });

        /** @var TrackingItem $item */
        $item = null;
        TrackingFacade::parent($this->parent)
            ->user(User::create([]))
            ->group(function () use (&$item) {
                $trackable = TrackingRedirect::create(['redirect' => 'https://www.example.com/']);
                $item = TrackingFacade::track($trackable);
            });

        $this->assertNotNull($item);
        $this->get(
            route('tracking', [
                'key' => $item->getKey(),
                'item' => $item->getRouteKey(),
            ])
        )
            ->assertStatus(401);
    }

    public function testShowTrackingInterfaceInvalid()
    {
        $this->markTestIncomplete();
    }

    public function testShowHttpException()
    {
        $this->markTestIncomplete();
    }

    public function testShowThrowable()
    {
        Schema::create('bad_test_models', function ($table) {
            $table->temporary();
            $table->id();
            $table->timestamps();
        });
        $this->artisan('migrate');

        /** @var TrackingItem $item */
        $item = null;
        TrackingFacade::parent($this->parent)
            ->group(function () use (&$item) {
                $item = TrackingFacade::track(BadTestModel::create());
            });

        $this->assertNotNull($item);
        $this->get(
            route('tracking', [
                'key' => $item->getKey(),
                'item' => $item->getRouteKey(),
            ])
        )
            ->assertStatus(500);
    }
}