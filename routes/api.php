<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\client\RecommendationController;
use App\Http\Controllers\Api\MovieApiController;

Route::prefix('v1')->group(function () {
    Route::get('/home', [MovieApiController::class, 'home'])->name('api.v1.home');
    Route::get('/movies', [MovieApiController::class, 'index'])->name('api.v1.movies.index');
    Route::get('/movies/{slug}', [MovieApiController::class, 'show'])->name('api.v1.movies.show');
});


Route::middleware(['web', 'auth:web'])->group(function () {

    Route::post(
        '/recommendations/preferences',
        [RecommendationController::class, 'savePreferences']
    )->name('api.recommendations.preferences');

});
