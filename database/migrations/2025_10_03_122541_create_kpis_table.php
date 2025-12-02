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
        Schema::create('kpis', function (Blueprint $table): void {
            $table->id();
            $table->text('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('data_source', 50)->index();
            $table->string('source_type')->default('page');
            $table->string('category', 50)->index();
            $table->text('formula')->nullable();
            $table->string('format', 50)->default('number');
            $table->timestamps();
            $table->decimal('target_value', 15, 2)->nullable();
            $table->date('target_date')->nullable();
            $table->date('from_date')->nullable();
            $table->date('comparison_start_date')->nullable();
            $table->date('comparison_end_date')->nullable();
            $table->string('goal_type', 50)->nullable();
            $table->string('page_path')->nullable();
            $table->string('value_type', 50)->nullable();
            $table->string('metric_type', 50)->nullable();
            $table->boolean('is_active')->index()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpis');
    }
};
