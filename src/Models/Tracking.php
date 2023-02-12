<?php

namespace KieranFYI\Tracking\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use KieranFYI\Logging\Traits\LoggingTrait;
use KieranFYI\Misc\Traits\HashTrait;
use KieranFYI\Misc\Traits\ImmutableTrait;
use KieranFYI\Services\Core\Traits\Serviceable;

/**
 * @property Model $parent
 */
class Tracking extends Model
{
    use SoftDeletes;
    use LoggingTrait;
    use Serviceable;
    use ImmutableTrait;
    use HashTrait;

    /**
     * @var string
     */
    protected $table = 'tracking';

    /**
     * @return MorphTo
     */
    public function parent(): MorphTo
    {
        return $this->morphTo();
    }
}