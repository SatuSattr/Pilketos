<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CalonController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DisplayKeyController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\VotingController;
use Illuminate\Support\Facades\Route;

// Halaman voting publik
Route::get('/', [VotingController::class, 'index'])->name('voting.index');
Route::post('/vote', [VotingController::class, 'vote'])->name('voting.vote');
Route::post('/display-key/validate', [VotingController::class, 'validateDisplayKey'])->name('voting.validate-key');

// Admin auth
Route::prefix('admin')->name('admin.')->group(function () {
    // /admin → redirect ke dashboard atau login
    Route::get('/', function () {
        return auth()->check()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
    });

    Route::get('login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
    Route::post('login', [AuthController::class, 'login'])->name('login.post')->middleware('guest');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
        Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

        // Calon CRUD
        Route::resource('calon', CalonController::class)->except(['create', 'edit']);

        // Voter (daftar hak suara)
        Route::resource('voter', VoterController::class)->except(['show', 'create', 'edit']);
        Route::post('voter/import', [VoterController::class, 'import'])->name('voter.import');
        Route::post('voter/{voter}/reset', [VoterController::class, 'resetVote'])->name('voter.reset');

        // Display keys
        Route::resource('display-key', DisplayKeyController::class)->except(['show', 'create', 'edit', 'update']);
        Route::post('display-key/{displayKey}/toggle', [DisplayKeyController::class, 'toggle'])->name('display-key.toggle');
        Route::post('display-key/{displayKey}/reset-stats', [DisplayKeyController::class, 'resetStats'])->name('display-key.reset-stats');
    });
});
