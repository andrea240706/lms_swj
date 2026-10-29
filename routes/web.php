<?php

use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/api/kalender-pendidikan', [AcademicCalendarController::class, 'index'])
    ->name('api.kalender-pendidikan');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/materi/{material}/files/{file}/download', [MaterialController::class, 'download'])
        ->scopeBindings()
        ->name('materials.files.download');
});

Route::middleware(['auth', 'verified', 'role:guru,teacher,admin,super_admin'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::post('/materi', [MaterialController::class, 'store'])->name('materi.store');
        Route::patch('/materi/{material}', [MaterialController::class, 'update'])->name('materi.update');
        Route::delete('/materi/{material}', [MaterialController::class, 'destroy'])->name('materi.destroy');
        Route::post('/materi/{material}/files', [MaterialController::class, 'storeFiles'])
            ->scopeBindings()
            ->name('materi.files.store');
        Route::delete('/materi/{material}/files/{file}', [MaterialController::class, 'destroyFile'])
            ->scopeBindings()
            ->name('materi.files.destroy');
    });

require __DIR__.'/auth.php';
