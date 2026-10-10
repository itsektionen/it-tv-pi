<?php

use App\Models\Slideshow;
use Illuminate\Support\Facades\Route;

Route::view('/tv/client.html', 'client')->name('client');
Route::get('/preview/{slideshow}', fn(Slideshow $slideshow) => view('client', [
    'state' => ['startedAt' => now()->getTimestampMs(), 'items' => $slideshow->clientItems()],
    'live' => false,
]))->name('preview');

Route::view('/', 'dashboard')->name('dashboard');