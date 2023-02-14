<?php

namespace KieranFYI\Tracking\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use KieranFYI\Logging\Traits\LoggingTrait;
use KieranFYI\Services\Core\Traits\Serviceable;
use KieranFYI\Tracking\Core\Interfaces\TrackingInterface;

/**
 * @property string $redirect
 */
class TrackingRedirect extends Model implements TrackingInterface
{
    use SoftDeletes;
    use LoggingTrait;
    use Serviceable;

    /**
     * @var string[]
     */
    protected $fillable = [
        'redirect'
    ];

    public function buildResponse(TrackingItem $item): RedirectResponse
    {
        return redirect()->away($this->redirect);
    }
}