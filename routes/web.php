<?php

use Illuminate\Support\Facades\Route;

Route::any(config('tracking.path') . '/{id}/{item}', function () {
    abort(418);
})
    ->name('tracking');