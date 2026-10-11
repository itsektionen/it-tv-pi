<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFolderRequest;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FolderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFolderRequest $request)
    {
        $folder = Folder::create($request->validated());

        return $request->expectsJson() ? response()->json($folder, 201) : back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Folder $folder)
    {
        DB::transaction(function () use ($folder) {
            $folder->children()->update(['parent_id' => $folder->parent_id]);
            $folder->images()->update(['folder_id' => null]);
            $folder->delete();
        });

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
