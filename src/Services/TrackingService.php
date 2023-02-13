<?php

namespace KieranFYI\Tracking\Core\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use KieranFYI\Tracking\Core\Facades\Tracking as TrackingFacade;
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
     * @var User|null
     */
    private ?User $user = null;

    /**
     * @param Model $parent
     * @return TrackingService
     */
    public function parent(Model $parent): static
    {
        $this->tracking = Tracking::where('parent_type', $parent->getMorphClass())
            ->where('parent_id', $parent->getKey())
            ->first();

        if (is_null($this->tracking)) {
            $this->tracking = new Tracking();
            $this->tracking->parent()->associate($parent);
            $this->tracking->save();
        }

        return $this;
    }

    /**
     * @return Tracking|null
     */
    public function tracking(): ?Tracking
    {
        return $this->tracking;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function user(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param callable $callable
     * @return void
     */
    public function group(callable $callable): void
    {
        TrackingFacade::swap($this);
        $callable();
        TrackingFacade::clearResolvedInstances();
    }

    /**
     * @param Model $trackable
     * @param User|null $user
     * @return TrackingItem
     */
    public function track(Model $trackable): TrackingItem
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

        if (!is_null($this->user)) {
            $item->where('user_type', $this->user->getMorphClass())
                ->where('user_id', $this->user->getKey());
        }
        $item = $item->first();

        if (is_null($item)) {
            $item = new TrackingItem();
            $item->tracking()->associate($this->tracking);
            $item->trackable()->associate($trackable);
            $item->user()->associate($this->user);
            $item->createdBy()->associate(Auth::user());
            $item->save();
        }

        return $item;
    }

    /**
     * @param string $url
     * @param User|null $user
     * @return TrackingItem
     */
    public function redirect(string $url): TrackingItem
    {
        if (is_null($this->tracking)) {
            throw new TypeError(self::class . '::track(): $tracking must be of type ' . Tracking::class);
        }

        $item = TrackingItem::where('tracking_id', $this->tracking->getKey())
            ->whereHasMorph('trackable', [TrackingRedirect::class], function (Builder $query) use ($url) {
                $query->where('redirect', $url);
            });

        if (!is_null($this->user)) {
            $item->where('user_type', $this->user->getMorphClass())
                ->where('user_id', $this->user->getKey());
        }

        $item = $item->first();
        if (!is_null($item)) {
            return $item;
        }

        $trackable = TrackingRedirect::create([
            'redirect' => $url
        ]);

        return $this->track($trackable);
    }

}