<?php

namespace KieranFYI\Tracking\Core\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use KieranFYI\Logging\Traits\LoggingTrait;
use KieranFYI\Misc\Traits\HashTrait;
use KieranFYI\Misc\Traits\ImmutableTrait;
use KieranFYI\Services\Core\Traits\Serviceable;

/**
 * @property array $data
 * @property Tracking $tracking
 * @property null|Authenticatable $user
 * @property null|Authenticatable $createdBy
 * @property Model $trackable
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
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return route('tracking', [
            'id' => $this->getKey(),
            'item' => $this->getRouteKey(),
        ]);
    }
}