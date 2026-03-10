<?php

namespace App\Http\Controllers\API;

use App\Enums\UserRoleEnums;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $authUser = Auth::user();
        if (! $authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($authUser->role->value === UserRoleEnums::ADMIN->value) {
            $users = User::query()
                ->select(['id', 'name', 'role', 'avatar'])
                ->latest('id')
                ->paginate(25);

            return response()->json($users, 200);
        }

        return response()->json([
            [
                'id' => $authUser->id,
                'name' => $authUser->name,
                'role' => $authUser->role?->value,
                'avatar' => $authUser->avatar ? Storage::url($authUser->avatar) : null,
            ],
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResponse
    {
        $authUser = Auth::user();
        if (! $authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $isAdmin = $authUser->role->value === UserRoleEnums::ADMIN->value;
        if (! $isAdmin && (int) $authUser->id !== (int) $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role?->value,
            'avatar' => $user->avatar ? Storage::url($user->avatar) : null,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function getAvatar($id): JsonResponse
    {
        $user = User::find($id);
        $authUser = Auth::user();

        if (! $authUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $isAdmin = $authUser->role->value === UserRoleEnums::ADMIN->value;
        if (! $isAdmin && (int) $authUser->id !== (int) $user->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json(['avatar_path' => $user->avatar], 200);
    }
}
