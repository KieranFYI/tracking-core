<?php

namespace KieranFYI\Tests\Tracking\Core\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use KieranFYI\Tracking\Core\Interfaces\TrackingInterface;
use KieranFYI\Tracking\Core\Models\TrackingItem;
use Symfony\Component\HttpFoundation\Response;

class BadTestModel extends Model implements TrackingInterface
{

    /**
     * @param TrackingItem $item
     * @return Response
     * @throws Exception
     */
    public function buildResponse(TrackingItem $item): Response
    {
        throw new Exception('Testing');
    }
}