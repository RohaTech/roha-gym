<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Helper\Type\UserStatus\UserStatusActive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Translation\Message;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'address'  => 'required|string|max:500',
            'logo'     => 'nullable|image|mimes:jpeg,png,webp|max:2048', // 2MB 
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $user = User::create([
            'name'      => $request->name,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
            'address'   => $request->address,
            'status'    => USER_STATUS_ACTIVE,
            'logo_path' => $logoPath,
            'role'      => 'user', // Default role for new registrations
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('phone', 'password'))) {
            return response()->json([
                'message' => Message::get('invalid_credentials'),
            ], 401);
        }

        $user = Auth::user();

        if ($user->status !== USER_STATUS_ACTIVE) {
            Auth::logout();
            return response()->json([
                'message' => Message::get('account_inactive'),
            ], 403);
        }

        // Revoke all existing tokens before issuing a new one
        $user->tokens()->delete();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => Message::get('successfully_loggedout'),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
