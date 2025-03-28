<?php

use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::controller(MovieController::class)->group(function () {
    Route::get('/', 'index')->name('movies.index');
    Route::post('/{movie}', 'update')->name('movies.update');
    Route::delete('/{movie}', 'destroy')->name('movies.destroy');
    Route::post('/', 'store')->name('movies.store');
});

// Route::get('dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// require __DIR__.'/settings.php';
// require __DIR__.'/auth.php';
