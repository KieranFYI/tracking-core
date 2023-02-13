<?php

use Illuminate\Support\Facades\Route;
use KieranFYI\Tracking\Core\Http\Controllers\TrackingController;

Route::any(config('tracking.path') . '/{key}/{item}', [TrackingController::class, 'show'])
    ->middleware('web')
    ->name('tracking');