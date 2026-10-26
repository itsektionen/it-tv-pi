<?php

use App\Http\Controllers\SessionController;
use App\Models\Screen;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [SessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::get('/client', function (Request $request) {
    $validator = Validator::make($request->query(), ['id' => ['required', 'string', 'max:64']]);

    if ($validator->fails()) {
        return response()->view('client', ['error' => $validator->errors()->first('id')], 400);
    }

    return view('client', ['screen' => Screen::connect($validator->validated()['id'])]);
})->name('client');
Route::get('/preview/{slideshow}', fn(Slideshow $slideshow) => view('client', [
    'state' => ['startedAt' => now()->getTimestampMs(), 'items' => $slideshow->clientItems()],
]))->name('preview');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [SessionController::class, 'destroy'])->name('logout');

    Route::middleware('can:viewer')->group(function () {
        Route::view('/', 'slideshows')->name('slideshows');
        Route::view('/screens', 'screens')->name('screens');
        Route::view('/presets', 'presets')->name('presets');
        Route::view('/images', 'images')->name('images');
    });

    Route::view('/users', 'users')->middleware('can:admin')->name('users');
});
