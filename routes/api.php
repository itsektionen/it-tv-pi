<?php

use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\ScreenController;
use App\Http\Controllers\Api\ScreenGroupController;
use App\Http\Controllers\Api\SlideshowController;
use Illuminate\Support\Facades\Route;

Route::prefix("v1")->group(function () {
    Route::apiResource("images", ImageController::class)->only("show");
    Route::get("images/preview/{image}", [ImageController::class, "showPreview"])->name("images.showPreview");

    Route::apiResource("images", ImageController::class)->only(["store", "update", "destroy"]);
    Route::apiResource("folders", FolderController::class)->only(["store", "destroy"]);
    Route::apiResource("slideshows", SlideshowController::class)->only(["store", "update", "destroy"]);
    Route::apiResource("screens", ScreenController::class)->only(["update", "destroy"]);
    Route::post("screens/{screen}/activate", [ScreenController::class, "activate"])->name("screens.activate");
    Route::apiResource("screen-groups", ScreenGroupController::class)->only(["store", "update", "destroy"]);
    Route::post("screen-groups/{screen_group}/activate", [ScreenGroupController::class, "activate"])->name("screen-groups.activate");
});
