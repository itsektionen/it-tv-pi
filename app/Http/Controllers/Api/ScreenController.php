<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Screen;
use App\Models\Slideshow;
use Illuminate\Http\Request;

class ScreenController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Screen $screen)
    {
        $screen->update($request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]));

        return $request->expectsJson() ? response()->json($screen) : back();
    }

    public function activate(Request $request, Screen $screen)
    {
        $validated = $request->validate([
            'slideshow_id' => ['nullable', 'integer', 'exists:slideshows,id'],
        ]);

        Screen::showSlideshow($screen->newCollection([$screen]), Slideshow::find($validated['slideshow_id'] ?? null));

        return $request->expectsJson() ? response()->json($screen->refresh()) : back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Screen $screen)
    {
        $screen->delete();

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
