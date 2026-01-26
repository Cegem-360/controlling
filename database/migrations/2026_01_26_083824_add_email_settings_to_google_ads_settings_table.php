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
        Schema::table('google_ads_settings', function (Blueprint $table) {
            $table->boolean('email_enabled')->default(false)->after('last_sync_at');
            $table->json('email_recipients')->nullable()->after('email_enabled');
            $table->string('email_frequency', 20)->default('monthly')->after('email_recipients');
            $table->unsignedTinyInteger('email_day_of_week')->nullable()->after('email_frequency');
            $table->unsignedTinyInteger('email_day_of_month')->default(1)->after('email_day_of_week');
            $table->timestamp('last_email_sent_at')->nullable()->after('email_day_of_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('google_ads_settings', function (Blueprint $table) {
            $table->dropColumn([
                'email_enabled',
                'email_recipients',
                'email_frequency',
                'email_day_of_week',
                'email_day_of_month',
                'last_email_sent_at',
            ]);
        });
    }
};
