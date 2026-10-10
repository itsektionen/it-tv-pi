<?php

use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\SlideshowController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
    Route::apiResource("images", ImageController::class)->only("show");

    Route::middleware(["auth:sanctum", "can:manager"])->group(function () {
        Route::apiResource("images", ImageController::class)->only("store");
        Route::apiResource("slideshows", SlideshowController::class)->only(["store", "update", "destroy"]);
        Route::post("slideshows/{slideshow}/activate", [SlideshowController::class, "activate"])->name("slideshows.activate");
    });

    Route::middleware(["auth:sanctum", "can:admin"])->group(function () {
        Route::apiResource("users", UserController::class)->only(["store", "update", "destroy"]);
    });
});
