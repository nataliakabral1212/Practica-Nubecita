<?php

use App\Http\Controllers\TotpEnrollmentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/inicio');

Route::middleware('auth')->group(function (): void {
    Route::get('/configurar-totp', [TotpEnrollmentController::class, 'show'])->name('totp.setup');
    Route::post('/configurar-totp', [TotpEnrollmentController::class, 'confirm'])
        ->middleware('throttle:5,1')->name('totp.confirm');

    Route::get('/inicio', fn () => response()
        ->view('dashboard', ['hostname' => gethostname()])
        ->header('Cache-Control', 'no-store'))
        ->middleware('totp.confirmed')->name('dashboard');
});
