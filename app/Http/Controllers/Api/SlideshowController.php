<?php

namespace App\Http\Controllers\Api;

use App\Events\ActiveSlideshowChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlideshowRequest;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SlideshowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSlideshowRequest $request)
    {
        $validated = $request->validated();

        $slideshow = DB::transaction(function () use ($request, $validated) {
            $slideshow = $request->user()->slideshows()->create(['name' => $validated['name']]);
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

        if ($slideshow->active) {
            $this->restartClients();
        }

        return response()->json($slideshow->load('items'));
    }

    public function activate(Request $request, Slideshow $slideshow)
    {
        DB::transaction(fn() => Slideshow::withoutTimestamps(function () use ($slideshow) {
            Slideshow::whereKeyNot($slideshow->id)->update(['active' => false]);
            $slideshow->forceFill(['active' => true])->save();
        }));

        $this->restartClients();

        return $request->expectsJson() ? response()->json($slideshow) : back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Slideshow $slideshow)
    {
        DB::transaction(function () use ($slideshow) {
            $slideshow->items()->delete();
            $slideshow->delete();
        });

        if ($slideshow->active) {
            $this->restartClients();
        }

        return $request->expectsJson() ? response()->noContent() : back();
    }

    private function restartClients(): void
    {
        Cache::forever('slideshow.started_at', now()->getTimestampMs());

        rescue(fn() => ActiveSlideshowChanged::dispatch());
    }
}
