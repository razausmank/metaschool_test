<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * register new user
     *
     * @param  Request $request
     * @return Json
     */
    public function register(Request $request)
    {
        $validated_fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $validated_fields['name'],
            'email' => $validated_fields['email'],
            'password' => bcrypt($validated_fields['password'])
        ]);

        $token = $user->createToken('metaschool_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }


    /**
     * login
     *
     * @param  Request $request
     * @return Json
     */
    public function login(Request $request)
    {
        $validated_fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // check if user exists with email
        $user = User::where('email', $validated_fields['email'])->first();

        // check if password matches
        if (!$user || !Hash::check($validated_fields['password'], $user->password)) {
            return response([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $token = $user->createToken('metaschool_token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }


    /**
     * logout and delete all tokens
     *
     * @param  mixed $request
     * @return Array
     */
    public function logout(Request $request)
    {
        // delete all tokens of the user
        auth()->user()->tokens()->delete();

        return  [
            'message' => 'Logged out'
        ];
    }
}
