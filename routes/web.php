<?php

use App\Models\Screen;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

Route::get('/tv/client.html', function (Request $request) {
    $validator = Validator::make($request->query(), ['id' => ['required', 'string', 'max:64', 'alpha_dash:ascii']]);

    if ($validator->fails()) {
        return response()->view('client', ['error' => $validator->errors()->first('id')], 400);
    }

    return view('client', ['screen' => Screen::connect($validator->validated()['id'])]);
})->name('client');
Route::get('/preview/{slideshow}', fn(Slideshow $slideshow) => view('client', [
    'state' => ['startedAt' => now()->getTimestampMs(), 'items' => $slideshow->clientItems()],
]))->name('preview');

Route::view('/', 'slideshows')->name('slideshows');
Route::view('/screens', 'screens')->name('screens');
Route::view('/images', 'images')->name('images');