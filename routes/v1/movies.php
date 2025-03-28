<?php

use App\Http\Controllers\Api\V1\MovieController;
use Illuminate\Support\Facades\Route;

Route::prefix('movies')->middleware('auth:sanctum')->controller(MovieController::class)->group(function () {
    Route::get('/', 'index')
        ->name('movies.index');

    Route::post('/', 'store')
        ->name('movies.store');

    Route::get('/{movie}', 'show')
        ->name('movies.show');

    Route::put('/{movie}', 'update')
        ->name('movies.update');

    Route::delete('/{movie}', 'destroy')
        ->name('movies.destroy');
});
