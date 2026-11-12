<?php

use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ScreenController;
use App\Http\Controllers\Api\ScreenGroupController;
use App\Http\Controllers\Api\PresetController;
use App\Http\Controllers\Api\SlideshowController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
    Route::apiResource("media", MediaController::class)->only("show")->parameters(["media" => "media"]);
    Route::get("media/preview/{media}", [MediaController::class, "showPreview"])->name("media.showPreview");

    Route::middleware(["auth:sanctum,api-key", "can:manager"])->group(function () {
        Route::apiResource("media", MediaController::class)->only("store");
        Route::apiResource("folders", FolderController::class)->only("store");
        Route::apiResource("slideshows", SlideshowController::class)->only(["store", "update"]);
        Route::post("screens/{screen}/activate", [ScreenController::class, "activate"])->name("screens.activate");
        Route::apiResource("screen-groups", ScreenGroupController::class)->only("store");
        Route::post("screen-groups/{screen_group}/activate", [ScreenGroupController::class, "activate"])->name("screen-groups.activate");
        Route::apiResource("presets", PresetController::class)->only("store");
        Route::post("presets/{preset}/activate", [PresetController::class, "activate"])->name("presets.activate");
    });

    Route::middleware(["auth:sanctum,api-key", "can:admin"])->group(function () {
        Route::apiResource("media", MediaController::class)->only(["update", "destroy"])->parameters(["media" => "media"]);
        Route::apiResource("folders", FolderController::class)->only("destroy");
        Route::apiResource("slideshows", SlideshowController::class)->only("destroy");
        Route::apiResource("screens", ScreenController::class)->only(["update", "destroy"]);
        Route::apiResource("screen-groups", ScreenGroupController::class)->only(["update", "destroy"]);
        Route::apiResource("presets", PresetController::class)->only(["update", "destroy"]);
        Route::apiResource("users", UserController::class)->only(["store", "update", "destroy"]);
        Route::apiResource("api-keys", ApiKeyController::class)->only(["store", "destroy"]);
    });
});
