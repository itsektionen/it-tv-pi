<?php

use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\SlideshowController;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
    Route::apiResource("images", ImageController::class)->only("show");

    Route::apiResource("images", ImageController::class)->only("store");
    Route::apiResource("slideshows", SlideshowController::class)->only(["store", "update", "destroy"]);
    Route::post("slideshows/{slideshow}/activate", [SlideshowController::class, "activate"])->name("slideshows.activate");
});
