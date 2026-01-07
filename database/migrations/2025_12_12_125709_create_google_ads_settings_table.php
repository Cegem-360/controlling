<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('google_ads_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('customer_id')->nullable()->comment('Google Ads Customer ID (XXX-XXX-XXXX)');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_connected')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_ads_settings');
    }
};
