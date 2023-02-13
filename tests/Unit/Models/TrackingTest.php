<?php

namespace KieranFYI\Tests\Tracking\Core\Unit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use KieranFYI\Logging\Traits\LoggingTrait;
use KieranFYI\Misc\Traits\HashTrait;
use KieranFYI\Misc\Traits\ImmutableTrait;
use KieranFYI\Services\Core\Traits\Serviceable;
use KieranFYI\Tests\Tracking\Core\Models\TestModel;
use KieranFYI\Tests\Tracking\Core\TestCase;
use KieranFYI\Tracking\Core\Models\Tracking;

class TrackingTest extends TestCase
{

    /**
     * @var Tracking
     */
    private Tracking $model;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new Tracking();
    }

    public function testIsModel()
    {
        $this->assertInstanceOf(Model::class, $this->model);
    }

    public function testTraits()
    {
        $uses = class_uses_recursive(Tracking::class);
        $this->assertContains(SoftDeletes::class, $uses);
        $this->assertContains(LoggingTrait::class, $uses);
        $this->assertContains(Serviceable::class, $uses);
        $this->assertContains(ImmutableTrait::class, $uses);
        $this->assertContains(HashTrait::class, $uses);
    }

    public function testTable()
    {
        $this->assertEquals('tracking', $this->model->getTable());
    }

    public function testParent()
    {
        $this->artisan('migrate');

        $this->assertInstanceOf(MorphTo::class, $this->model->parent());
        $testModel = new TestModel();
        $this->model->parent()->associate($testModel);
        $this->assertTrue($testModel->is($this->model->parent));
    }
}