<?php

use App\Http\Controllers\SessionController;
use App\Models\Slideshow;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::view('/tv/client.html', 'client')->name('client');
Route::get('/preview/{slideshow}', fn(Slideshow $slideshow) => view('client', [
    'state' => ['startedAt' => now()->getTimestampMs(), 'items' => $slideshow->clientItems()],
    'live' => false,
]))->name('preview');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

    Route::view('/', 'dashboard')->middleware('can:manager')->name('dashboard');
    Route::view('/users', 'users')->middleware('can:admin')->name('users');
});