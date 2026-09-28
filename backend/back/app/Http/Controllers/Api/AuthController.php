<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{

    public function auth(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'balance' => 0.00
        ]);

        $token = $user->createToken('auth_token')->PlainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);

    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password'
            ], 401);
        };

        $token = $user->createToken('auth_token')->PlainTextToken;

        return response()->json([
            'message' => 'User login successfully',
            'user' => $user,
            'access_token' => $token,
        ], 200);

    }

    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logget out successfully'
        ], 200);

    }

    public function me(Request $request)
    {

        return response()->json([
            'user' => $request->user 
        ], 200);

    }

}
