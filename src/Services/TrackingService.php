<?php

namespace KieranFYI\Tracking\Core\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use KieranFYI\Tracking\Core\Interfaces\TrackingInterface;
use KieranFYI\Tracking\Core\Models\Tracking;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use KieranFYI\Tracking\Core\Models\TrackingRedirect;
use TypeError;

class TrackingService
{
    /**
     * @var Tracking|null
     */
    private ?Tracking $tracking = null;

    /**
     * @param Model $parent
     * @param callable $callable
     * @return void
     */
    public function asParent(Model $parent, callable $callable): void
    {
        $this->tracking = Tracking::where('parent_type', $parent->getMorphClass())
            ->where('parent_id', $parent->getKey())
            ->first();

        if (is_null($this->tracking)) {
            $this->tracking = new Tracking();
            $this->tracking->parent()->associate($parent);
            $this->tracking->save();
        }

        $callable();
        $this->tracking = null;
    }

    /**
     * @return Tracking|null
     */
    public function tracking(): ?Tracking
    {
        return $this->tracking;
    }

    /**
     * @param Model $trackable
     * @param User|null $user
     * @return TrackingItem
     */
    public function track(Model $trackable, User $user = null): TrackingItem
    {
        if (is_null($this->tracking)) {
            throw new TypeError(self::class . '::track(): $tracking must be of type ' . Tracking::class);
        }
        if (!($trackable instanceof TrackingInterface)) {
            throw new TypeError(self::class . '::track(): $tracking must be of type ' . TrackingInterface::class);
        }

        $item = TrackingItem::where('tracking_id', $this->tracking->getKey())
            ->where('trackable_type', $trackable->getMorphClass())
            ->where('trackable_id', $trackable->getKey());

        if (!is_null($user)) {
            $item->where('user_type', $user->getMorphClass())
                ->where('user_id', $user->getKey());
        }
        $item = $item->first();

        if (is_null($item)) {
            $item = new TrackingItem();
            $item->tracking()->associate($this->tracking);
            $item->trackable()->associate($trackable);
            $item->user()->associate($user);
            $item->createdBy()->associate(Auth::user());
            $item->save();
        }

        return $item;
    }

    public function redirect(string $url, User $user = null)
    {
        if (is_null($this->tracking)) {
            throw new TypeError(self::class . '::track(): $tracking must be of type ' . Tracking::class);
        }

        $item = TrackingItem::where('tracking_id', $this->tracking->getKey())
            ->whereHasMorph('trackable', [TrackingRedirect::class], function (Builder $query) use ($url) {
                $query->where('redirect', $url);
            });

        if (!is_null($user)) {
            $item->where('user_type', $user->getMorphClass())
                ->where('user_id', $user->getKey());
        }

        $item = $item->first();
        if (!is_null($item)) {
            return $item;
        }

        $trackable = TrackingRedirect::create([
            'redirect' => $url
        ]);

        return $this->track($trackable, $user);
    }

}