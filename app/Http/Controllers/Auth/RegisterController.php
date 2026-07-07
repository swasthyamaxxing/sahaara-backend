<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function register (RegisterUserRequest $request) {
        try {
            DB::beginTransaction();

            // Validate the incoming requests
            $request->validated();

            // Extract the required fields
            $user_data = [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'naked_password' => $request->input('password'),
                'role' => $request->input('role'),
            ];
            
            // Fixed: Hash the password using the incoming request value
            $user_data['password'] = Hash::make($request->input('password'));

            // Create the user in the database
            $user = User::create($user_data);

            // Passport-specific authentication: Generate a personal access token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User signup successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Some unknown error occurred',
                'error' => $e->getMessage() // Good for debugging; remove in production
            ], 500);
        }
    }
}
