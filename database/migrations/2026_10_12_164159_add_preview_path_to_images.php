<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exceptions\ImageException;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Format;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $maxPixels = 25_000_000;

        if (!Schema::hasColumn('images', 'preview_path')) {
            Schema::table('images', function (Blueprint $table) {
                $table->string('preview_path')->nullable()->after('path');
            });
        }

        Storage::deleteDirectory("previews");

        foreach (\App\Models\Image::all() as $imageModel) {
            $filenameWithExtension = explode('/', $imageModel->path)[1];
            $filenameWithoutExtension = explode('.', $filenameWithExtension)[0];
            $previewPath = "previews/" . $filenameWithoutExtension . ".webp";

            try {
                $path = Storage::path($imageModel->path);
                [$width, $height] = getimagesize($path) ?: [0, 0];

                if ($width * $height > $maxPixels) {
                    throw new RuntimeException("Image is too large ({$width}x{$height})");
                }

                $preview = Image::decodePath($path)
                    ->scaleDown(width: 150)
                    ->encodeUsingFormat(Format::WEBP);

                Storage::put($previewPath, $preview->toString());
            } catch (ImageException | RuntimeException $e) {
                Log::warning("Could not create preview for {$imageModel->path}: {$e->getMessage()}");
                $previewPath = $imageModel->path;
            }

            $imageModel->update([
                "preview_path" => $previewPath
            ]);
        }

        Schema::table('images', function (Blueprint $table) {
            $table->string('preview_path')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('preview_path');
        });
    }
};
