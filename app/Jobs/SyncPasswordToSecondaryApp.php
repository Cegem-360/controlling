<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncPasswordToSecondaryApp implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email,
        public string $hashedPassword,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $secondaryAppUrl = config('services.secondary_app.url');
        $apiKey = config('services.secondary_app.api_key');

        if (! $secondaryAppUrl || ! $apiKey) {
            Log::warning('Secondary app configuration is missing. Skipping password sync.');

            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept' => 'application/json',
            ])
                ->timeout(10)
                ->post("{$secondaryAppUrl}/api/sync-password", [
                    'email' => $this->email,
                    'password_hash' => $this->hashedPassword,
                ]);

            if ($response->successful()) {
                Log::info("Password synced successfully for user {$this->email} to secondary app.");
            } else {
                Log::error("Failed to sync password for user {$this->email}. Status: {$response->status()}");
                throw new \Exception("Password sync failed with status {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::error("Exception during password sync for user {$this->email}: {$e->getMessage()}");
            throw $e;
        }
    }
}
