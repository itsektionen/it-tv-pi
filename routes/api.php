<?php

use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\ScreenController;
use App\Http\Controllers\Api\ScreenGroupController;
use App\Http\Controllers\Api\PresetController;
use App\Http\Controllers\Api\SlideshowController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
    Route::apiResource("images", ImageController::class)->only("show");
    Route::get("images/preview/{image}", [ImageController::class, "showPreview"])->name("images.showPreview");

    Route::middleware(["auth:sanctum", "can:manager"])->group(function () {
        Route::apiResource("images", ImageController::class)->only("store");
        Route::apiResource("folders", FolderController::class)->only("store");
        Route::apiResource("slideshows", SlideshowController::class)->only(["store", "update"]);
        Route::post("screens/{screen}/activate", [ScreenController::class, "activate"])->name("screens.activate");
        Route::apiResource("screen-groups", ScreenGroupController::class)->only("store");
        Route::post("screen-groups/{screen_group}/activate", [ScreenGroupController::class, "activate"])->name("screen-groups.activate");
        Route::apiResource("presets", PresetController::class)->only("store");
        Route::post("presets/{preset}/activate", [PresetController::class, "activate"])->name("presets.activate");
    });

    Route::middleware(["auth:sanctum", "can:admin"])->group(function () {
        Route::apiResource("images", ImageController::class)->only(["update", "destroy"]);
        Route::apiResource("folders", FolderController::class)->only("destroy");
        Route::apiResource("slideshows", SlideshowController::class)->only("destroy");
        Route::apiResource("screens", ScreenController::class)->only(["update", "destroy"]);
        Route::apiResource("screen-groups", ScreenGroupController::class)->only(["update", "destroy"]);
        Route::apiResource("presets", PresetController::class)->only(["update", "destroy"]);
        Route::apiResource("users", UserController::class)->only(["store", "update", "destroy"]);
    });
});
