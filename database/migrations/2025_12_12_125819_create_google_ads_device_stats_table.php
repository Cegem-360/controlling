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
        Schema::create('google_ads_device_stats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->string('device')->comment('MOBILE, DESKTOP, TABLET, OTHER');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('conversions', 10, 2)->default(0);
            $table->decimal('ctr', 8, 4)->default(0);
            $table->decimal('avg_cpc', 10, 2)->default(0);
            $table->decimal('conversion_rate', 8, 4)->default(0);
            $table->timestamps();

            $table->unique(['team_id', 'date', 'device']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_ads_device_stats');
    }
};
