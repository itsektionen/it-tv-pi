<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScreenGroupRequest;
use App\Models\Screen;
use App\Models\ScreenGroup;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScreenGroupController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScreenGroupRequest $request)
    {
        $validated = $request->validated();

        $screenGroup = DB::transaction(function () use ($validated) {
            $screenGroup = ScreenGroup::create(['name' => $validated['name']]);
            $screenGroup->screens()->sync($validated['screens'] ?? []);

            return $screenGroup;
        });

        return $request->expectsJson() ? response()->json($screenGroup->load('screens'), 201) : back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreScreenGroupRequest $request, ScreenGroup $screenGroup)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($screenGroup, $validated) {
            $screenGroup->update(['name' => $validated['name']]);
            $screenGroup->screens()->sync($validated['screens'] ?? []);
        });

        return $request->expectsJson() ? response()->json($screenGroup->load('screens')) : back();
    }

    public function activate(Request $request, ScreenGroup $screenGroup)
    {
        $validated = $request->validate([
            'slideshow_id' => ['nullable', 'integer', 'exists:slideshows,id'],
        ]);

        Screen::showSlideshow($screenGroup->screens, Slideshow::find($validated['slideshow_id'] ?? null));

        return $request->expectsJson() ? response()->json($screenGroup->load('screens')) : back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ScreenGroup $screenGroup)
    {
        $screenGroup->delete();

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
