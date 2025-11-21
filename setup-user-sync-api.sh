#!/bin/bash

# Setup User Sync API for Secondary Apps
# Run this script in the secondary app root directory

echo "🚀 Setting up User Sync API..."

# 1. Create controller using artisan
php artisan make:controller Api/UserSyncController --no-interaction

# 2. Create middleware using artisan
php artisan make:middleware ValidateApiKey --no-interaction

# 3. Overwrite UserSyncController with actual implementation
cat > app/Http/Controllers/Api/UserSyncController.php << 'EOF'
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
EOF

echo "✅ UserSyncController created"

# 4. Overwrite ValidateApiKey middleware with actual implementation
cat > app/Http/Middleware/ValidateApiKey.php << 'EOF'
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = config('services.subscriber_api_key');

        if ($request->bearerToken() !== $apiKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
EOF

echo "✅ ValidateApiKey middleware created"

# 5. Add routes to api.php
if ! grep -q "UserSyncController" routes/api.php; then
    cat >> routes/api.php << 'EOF'

Route::middleware('api.key')->group(function () {
    Route::post('/create-user', [UserSyncController::class, 'create']);
    Route::post('/sync-user', [UserSyncController::class, 'sync']);
});
EOF
    echo "✅ API routes added to end of api.php"
    echo "⚠️  Add this import manually: use App\\Http\\Controllers\\Api\\UserSyncController;"
else
    echo "⚠️  Routes already exist in api.php"
fi

echo ""
echo "📋 Manual steps required:"
echo ""
echo "1. Add middleware alias to bootstrap/app.php:"
echo "   ->withMiddleware(function (Middleware \$middleware) {"
echo "       \$middleware->alias(['api.key' => \\App\\Http\\Middleware\\ValidateApiKey::class]);"
echo "   })"
echo ""
echo "2. Add to config/services.php:"
echo "   'subscriber_api_key' => env('SUBSCRIBER_API_KEY'),"
echo ""
echo "3. Add to .env:"
echo "   SUBSCRIBER_API_KEY=your-secret-key-here"
echo ""
echo "✅ Setup complete!"
