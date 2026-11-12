<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApiKeyRequest;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApiKeyRequest $request)
    {
        $apiKey = new ApiKey($request->validated());
        $token = $apiKey->generateToken();
        $apiKey->save();

        return response()->json([...$apiKey->toArray(), 'token' => $token], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ApiKey $apiKey)
    {
        $apiKey->delete();

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
