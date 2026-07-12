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
    // route definition -> function definition (before validate the incoming request)

    /**
     * Validates and processes the registration data for both caretakers and patients.
     * @param  \App\Http\Requests\RegisterUserRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @bodyParam fullName string Required. The full name of the user. Example: John Doe
     * @bodyParam email string Required. A unique email address. Example: john@example.com
     * @bodyParam age integer Required. The age of the user. Example: 30
     * @bodyParam password string Required. The user password (minimum length typically enforced).
     * @bodyParam confirmPassword string Required. Must match the password field.
     * @bodyParam gender string Required. The gender identity of the user.
     * @bodyParam role string Required. The user's account type. Must be either 'caretaker' or 'patient'.
     */
    public function register (RegisterUserRequest $request) {
        try {
            DB::beginTransaction();

            // Validate the incoming requests
            $request->validated();

            // Extract the required fields
            $user_data = [
                'name' => $request->input('fullName'),
                'email' => $request->input('email'),
                'age' => $request->input('age'),
                'naked_password' => $request->input('password'),
                'gender' => $request->input('gender'),
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
                'status' => true,
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
