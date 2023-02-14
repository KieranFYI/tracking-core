<?php

namespace KieranFYI\Tracking\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use KieranFYI\Misc\Traits\ImmutableTrait;
use KieranFYI\Services\Core\Traits\Serviceable;
use Illuminate\Foundation\Auth\User;

/**
 * @property string $ip
 * @property array $ips
 * @property bool $trusted_proxy
 * @property string $user_agent
 * @property string $referer
 * @property int $status_code
 * @property Tracking $tracking
 * @property TrackingItem $item
 * @property User $user
 */
class TrackingRequest extends Model
{
    use Serviceable;
    use ImmutableTrait;

    /**
     * @var string[]
     */
    protected $fillable = [
        'ip', 'ips', 'trusted_proxy', 'user_agent', 'referer', 'status_code'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'ips' => 'array',
        'trusted_proxy' => 'boolean',
        'status_code' => 'int',
    ];

    /**
     * @return BelongsTo
     */
    public function tracking(): BelongsTo
    {
        return $this->belongsTo(Tracking::class);
    }

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(TrackingItem::class, 'tracking_item_id');
    }

    /**
     * @return MorphTo
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}