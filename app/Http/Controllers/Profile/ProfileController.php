<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function show(Request $request, User $user)
    {
        
        $loggedin_user = $request->user();

        $is_self = $loggedin_user->id === $user->id;

        $is_assigned_caretaker = $loggedin_user->canAccessPatientData($user);

        if ( ! ( $is_self || $is_assigned_caretaker) ) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized request',
            ], 403);
        }

        return response()->json([
            'status' => true,
            'message' => 'User data retrieved successfully',
            'data' => $user
        ], 200);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
