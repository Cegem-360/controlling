<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserSyncCreateRequest;
use App\Http\Requests\Api\UserSyncRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

final class UserSyncController extends Controller
{
    public function create(UserSyncCreateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'email' => $validated['email'],
            'name' => $validated['name'],
            'password' => $validated['password_hash'],
        ]);

        $user->email_verified_at = now();
        $user->saveQuietly();
        $user->assignRole($validated['role']);

        return response()->json([
            'message' => 'User created successfully',
            'user_id' => $user->id,
        ], 201);
    }

    public function sync(UserSyncRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->firstOrFail();

        if (isset($validated['new_email'])) {
            $user->email = $validated['new_email'];
        }
        if (isset($validated['password_hash'])) {
            $user->password = $validated['password_hash'];
        }

        $user->saveQuietly();

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json(['message' => 'User synced successfully']);
    }
}
