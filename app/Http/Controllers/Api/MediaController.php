<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMediaRequest;
use App\Models\Media;
use App\Models\SlideshowItem;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class MediaController extends Controller
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
        $upload = $request->file('file');
        $isVideo = $upload instanceof UploadedFile && str_starts_with((string) $upload->getMimeType(), 'video/');

        $validated = $request->validate([
            'file' => ['required', Rule::when($isVideo, ['file', 'max:102400'], ['image', 'max:10240'])],
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
        ]);

        try {
            $media = Media::upload($validated['file'], $validated['file']->getClientOriginalName(), $validated['folder_id'] ?? null);
        } catch (RuntimeException $e) {
            throw ValidationException::withMessages(['file' => $e->getMessage()]);
        }

        return $request->expectsJson() ? response()->json($media, 201) : back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Media $media)
    {
        return response()->file(Storage::path($media->path), ['Cache-Control' => 'public, max-age=31536000, immutable']);
    }

    public function showPreview(Media $media)
    {
        return Storage::response($media->preview_path, headers: ['Cache-Control' => 'public, max-age=31536000, immutable']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Media $media)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMediaRequest $request, Media $media)
    {
        $media->update($request->validated());

        return $request->expectsJson() ? response()->json($media) : back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Media $media)
    {
        abort_if(SlideshowItem::where('data->media', $media->id)->exists(), 409, 'This media is used in a slideshow.');

        Storage::delete(array_unique([$media->path, $media->preview_path]));
        $media->delete();

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
