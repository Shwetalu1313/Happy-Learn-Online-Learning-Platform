<?php

namespace App\Http\Controllers;

use App\Models\UserRole;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titlePage = __('users.roles_ent');
        $users_roles = UserRole::all();
        return view('users.role_entry', compact('titlePage', 'users_roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(UserRole $userRole)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserRole $userRole)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserRole $userRole)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserRole $userRole)
    {
        //
    }

    /**
     * Request Multi data as bulk insertion and create the new records
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkInsert(Request $request): \Illuminate\Http\JsonResponse
    {
        $roles = $request->input('roles');

        // Validate for unique role names:
        // 1. Check for unique values and existing names in database
        $uniqueRoles = array_unique($roles);
        if (count($roles) !== count($uniqueRoles)) {
            return response()->json(['message' => 'Duplicate role names found'], 400);
        }

        // 2. Check against existing names in database
        $existingRoles = UserRole::whereIn('name', $uniqueRoles)->pluck('name');
        $duplicates = array_intersect($uniqueRoles, $existingRoles->toArray());

        if (!empty($duplicates)) {
            return response()->json([
                'message' => 'Duplicate role names already exist in the database: ' . implode(', ', $duplicates),
                'status' => 400
            ]);
        }

        // Proceed with insertion if no duplicates found
        $insertData = [];
        foreach ($roles as $role) {
            $insertData[] = ['name' => $role];
        }

        UserRole::insert($insertData);

        return response()->json(['message' => 'Roles inserted successfully'], 200);
    }



}
