<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\RecommendationController;


Route::middleware(['web', 'auth:web'])->group(function () {

    Route::post(
        '/recommendations/preferences',
        [RecommendationController::class, 'savePreferences']
    )->name('api.recommendations.preferences');

});