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
        Schema::table('global_settings', function (Blueprint $table): void {
            $table->string('google_ads_client_id')->nullable()->after('google_service_account');
            $table->text('google_ads_client_secret')->nullable()->after('google_ads_client_id');
            $table->string('google_ads_developer_token')->nullable()->after('google_ads_client_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'google_ads_client_id',
                'google_ads_client_secret',
                'google_ads_developer_token',
            ]);
        });
    }
};
