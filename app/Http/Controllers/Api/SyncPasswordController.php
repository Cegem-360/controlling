<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SyncPasswordRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class SyncPasswordController extends Controller
{
    /**
     * Sync password from secondary app.
     */
    public function __invoke(SyncPasswordRequest $request): JsonResponse
    {
        $email = $request->validated('email');
        $passwordHash = $request->validated('password_hash');

        try {
            $user = User::query()->where('email', $email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Update password WITHOUT triggering observers to prevent infinite loop
            User::withoutEvents(function () use ($user, $passwordHash): void {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['password' => $passwordHash]);
            });

            Log::info("Password synced successfully from secondary app for user {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Password synced successfully.',
            ]);
        } catch (Exception $e) {
            Log::error("Failed to sync password from secondary app: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => 'Failed to sync password.',
            ], 500);
        }
    }
}
