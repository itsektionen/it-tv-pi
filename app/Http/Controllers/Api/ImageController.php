<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateImageRequest;
use App\Models\SlideshowItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Exceptions\ImageException;
use Intervention\Image\Format;
use RuntimeException;
use \App\Models\Image;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:10240'],
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
        ]);

        $file = $validated['image'];

        $image = new Image();
        $image->id = $image->newUniqueId();
        $image->name = Str::limit($file->getClientOriginalName(), 255, '');
        $image->folder_id = $validated['folder_id'] ?? null;
        $image->path = $file->storeAs('images', "{$image->id}.{$file->extension()}");
        $image->preview_path = "previews/{$image->id}.webp";

        try {
            $path = Storage::path($image->path);
            [$width, $height] = getimagesize($path) ?: [0, 0];

            if ($width * $height > 25_000_000) {
                throw new RuntimeException("Image is too large ({$width}x{$height})");
            }

            $preview = \Intervention\Image\Laravel\Facades\Image::decodePath($path)
                ->scaleDown(width: 150)
                ->encodeUsingFormat(Format::WEBP);

            Storage::put($image->preview_path, $preview->toString());
        } catch (ImageException | RuntimeException $e) {
            Log::warning("Could not create preview for {$image->path}: {$e->getMessage()}");
            $image->preview_path = $image->path;
        }

        $image->save();

        return $request->expectsJson() ? response()->json($image, 201) : back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        return Storage::response($image->path, headers: ['Cache-Control' => 'public, max-age=31536000, immutable']);
    }

    public function showPreview(Image $image)
    {
        return Storage::response($image->preview_path, headers: ['Cache-Control' => 'public, max-age=31536000, immutable']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateImageRequest $request, Image $image)
    {
        $image->update($request->validated());

        return $request->expectsJson() ? response()->json($image) : back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Image $image)
    {
        abort_if(SlideshowItem::where('data->image', $image->id)->exists(), 409, 'This image is used in a slideshow.');

        Storage::delete($image->path);
        $image->delete();

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
