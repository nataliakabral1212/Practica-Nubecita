<?php

use App\Http\Controllers\TwoFactorSetupController;
use App\Http\Middleware\EnsureTwoFactorEnabled;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('two-factor/setup', TwoFactorSetupController::class)->middleware('auth')->name('two-factor.setup');

Route::middleware(['auth', EnsureTwoFactorEnabled::class, 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
