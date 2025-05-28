<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Handle admin login.
     */
    public function login(Request $request)
    {
        // Validate login credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Generate token for the authenticated user
            $token = $user->createToken('AdminToken')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        }

        // Return an error if authentication fails
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Handle admin logout.
     */
    public function logout(Request $request)
    {
        if ($request->user()) {
            // Revoke the current access token
            $request->user()->currentAccessToken()->delete();

            return response()->json(['success' => true, 'message' => 'Logged out successfully']);
        }

        return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
    }
}
