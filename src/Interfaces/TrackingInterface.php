<?php

namespace KieranFYI\Tracking\Core\Interfaces;

use KieranFYI\Tracking\Core\Models\TrackingItem;
use Symfony\Component\HttpFoundation\Response;

interface TrackingInterface
{
    public function buildResponse(TrackingItem $item): Response;
}