<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlideshowRequest;
use App\Models\Screen;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SlideshowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSlideshowRequest $request)
    {
        $validated = $request->validated();

        $slideshow = DB::transaction(function () use ($validated) {
            $slideshow = Slideshow::create(['name' => $validated['name']]);
            $slideshow->items()->createMany($validated['sections']);

            return $slideshow;
        });

        return response()->json($slideshow->load('items'), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreSlideshowRequest $request, Slideshow $slideshow)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($slideshow, $validated) {
            $slideshow->update(['name' => $validated['name']]);
            $slideshow->items()->delete();
            $slideshow->items()->createMany($validated['sections']);
        });

        Screen::showSlideshow($slideshow->screens, $slideshow);

        return response()->json($slideshow->load('items'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Slideshow $slideshow)
    {
        $screens = $slideshow->screens;

        DB::transaction(function () use ($slideshow) {
            $slideshow->items()->delete();
            $slideshow->delete();
        });

        Screen::showSlideshow($screens, null);

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
