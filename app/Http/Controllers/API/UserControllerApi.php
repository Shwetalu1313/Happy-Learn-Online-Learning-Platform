<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserControllerApi extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users,200);
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
    public function show(User $user)
    {
        if ($user) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'points' => $user->points,
                'birthdate' => $user->birthdate,
                'avatar' => Storage::url($user->avatar),
            ], 200);
        } else {
            return response()->json(['error' => "User not found"], 404);
        }
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

    public function getAvatar($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json(['avatar_path' => $user->avatar], 200);
        } else {
            return response()->json(['error' => "User not found"], 404);
        }
    }
}
