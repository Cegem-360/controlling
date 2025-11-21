<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserSyncController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
            'password_hash' => 'required|string',
            'role' => 'required|string|in:subscriber,manager',
        ]);

        $user = User::create([
            'email' => $validated['email'],
            'name' => $validated['name'],
            'password' => $validated['password_hash'],
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user_id' => $user->id,
        ], 201);
    }

    public function sync(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($request->has('new_email')) {
            $user->email = $request->new_email;
        }
        if ($request->has('password_hash')) {
            $user->password = $request->password_hash;
        }
        if ($request->has('role')) {
            $user->role = $request->role;
        }

        $user->save();

        return response()->json(['message' => 'User synced successfully']);
    }
}
