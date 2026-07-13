<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

class LoginController extends Controller
{
    /**
     * Handle user authentication and return Passport access token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login (Request $request)
    {
        try {

            $email = $request->input('email');

            // Check if user with given email exists
            $user = User::where('email', $email)->first();

            if (! $user) {
                return response()->json([
                    'status' => false,
                    'message' => 'The provided credentials do not match our record.',
                ], 401);
            }

            // Check for the password
            if (! Hash::check($request->input('password'), $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The provided credentials do not match our record.'
                ], 401);
            }

            // Login the user
            // Passport-specific authentication: Generate a personal access token
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->accessToken;

            return response()->json([
                'status' => true,
                'message' => 'User login successful!',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' =>  'Something went wrong!',
            ], 500);
        }
    }
}
