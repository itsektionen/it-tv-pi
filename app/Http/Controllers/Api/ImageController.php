<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        ]);

        $file = $validated['image'];

        $image = new Image();
        $image->id = $image->newUniqueId();
        $image->path = $file->storeAs('images', "{$image->id}.{$file->extension()}");
        $image->save();

        return response()->json($image, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image)
    {
        return Storage::response($image->path);
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
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        //
    }
}
