<?php

namespace KieranFYI\Tracking\Core\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User;
use KieranFYI\Logging\Traits\LoggingTrait;
use KieranFYI\Misc\Traits\HashTrait;
use KieranFYI\Misc\Traits\ImmutableTrait;
use KieranFYI\Services\Core\Traits\Serviceable;
use KieranFYI\Tracking\Core\Interfaces\TrackingInterface;

/**
 * @property int $tracking_id
 * @property Tracking $tracking
 * @property null|User $user
 * @property null|User $createdBy
 * @property TrackingInterface $trackable
 * @property Collection $requests
 * @property string $url
 */
class TrackingItem extends Model
{
    use SoftDeletes;
    use LoggingTrait;
    use Serviceable;
    use ImmutableTrait;
    use HashTrait;

    /**
     * @return BelongsTo
     */
    public function tracking(): BelongsTo
    {
        return $this->belongsTo(Tracking::class);
    }

    /**
     * @return MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany
     */
    public function requests(): HasMany
    {
        return $this->hasMany(TrackingRequest::class);
    }

    /**
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return route('tracking', [
            'key' => $this->getKey(),
            'item' => $this->getRouteKey(),
        ]);
    }
}