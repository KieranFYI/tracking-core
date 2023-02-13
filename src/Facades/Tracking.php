<?php

namespace KieranFYI\Tracking\Core\Facades;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Facade;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Services\TrackingService;

/**
 * @method static TrackingService parent(Model $parent)
 * @method static null|Tracking tracking()
 * @method static TrackingService user(User $user)
 * @method static void group(callable $callable)
 * @method static TrackingItem track(Model $trackable, User $user = null)
 * @method static TrackingItem redirect(string $url, User $user = null)
 *
 * @see TrackingService
 */
class Tracking extends Facade
{
    /**
     * @var bool
     */
    protected static $cached = false;

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