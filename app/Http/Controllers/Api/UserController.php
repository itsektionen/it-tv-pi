<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        return response()->json(User::create($request->validated()), 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $user->update(array_filter($request->validated(), fn($value) => $value !== null));

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 422, 'You cannot remove your own account.');

        $user->delete();

        return $request->expectsJson() ? response()->noContent() : back();
    }
}
