<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePresetRequest;
use App\Http\Requests\StoreScreenGroupRequest;
use App\Models\Preset;
use App\Models\Screen;
use App\Models\ScreenGroup;
use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresetController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePresetRequest $request)
    {
        $validated = $request->validated();
        $effects = $request->effects();

        $preset = DB::transaction(function () use ($validated, $effects) {
            $preset = Preset::create(['name' => $validated['name'], 'data' => $effects]);

            return $preset;
        });

        return $request->expectsJson() ? response()->json($preset, 201) : back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePresetRequest $request, Preset $preset)
    {
        $validated = $request->validated();
        $effects = $request->effects();

        DB::transaction(function () use ($preset, $validated, $effects) {
            $preset->update(['name' => $validated['name'], 'data' => $effects]);
        });

        return $request->expectsJson() ? response()->json($preset) : back();
    }

    public function activate(Request $request, Preset $preset)
    {
        $effects = collect($preset->data);
        $screenGroups = ScreenGroup::with('screens')->findMany($effects->pluck('screen_group_id'))->keyBy('id');
        $slideshows = Slideshow::findMany($effects->pluck('slideshow_id'))->keyBy('id');

        foreach ($effects as $effect) {
            $screenGroup = $screenGroups->get($effect['screen_group_id']);
            $slideshow = $slideshows->get($effect['slideshow_id']);

            if ($screenGroup && $slideshow) {
                Screen::showSlideshow($screenGroup->screens, $slideshow);
            }
        }

        $preset->update(['last_activated' => now()]);

        return $request->expectsJson() ? response()->json($preset) : back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Preset $preset)
    {
        $preset->delete();

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
