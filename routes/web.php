<?php

use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\CompetitorController;
use App\Http\Controllers\Admin\MeasureController;
use App\Http\Controllers\Admin\StageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes (referee only)
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('competitions', CompetitionController::class);
    Route::resource('competitors', CompetitorController::class);

    // Stage routes
    Route::post('competitions/{competition}/stages', [StageController::class, 'store'])->name('competitions.stages.store');
    Route::put('stages/{stage}', [StageController::class, 'update'])->name('stages.update');
    Route::delete('stages/{stage}', [StageController::class, 'destroy'])->name('stages.destroy');

    // Measure routes
    Route::post('stages/{stage}/measures', [MeasureController::class, 'store'])->name('stages.measures.store');
    Route::put('measures/{measure}', [MeasureController::class, 'update'])->name('measures.update');
    Route::delete('measures/{measure}', [MeasureController::class, 'destroy'])->name('measures.destroy');
});

require __DIR__.'/auth.php';
