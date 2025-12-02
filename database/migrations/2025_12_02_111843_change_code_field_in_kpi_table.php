<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::table('kpis', function (Blueprint $table): void {
                $table->dropUnique(['code']);
            });
        } catch (QueryException) {
            // Index already dropped
        }

        Schema::table('kpis', function (Blueprint $table): void {
            $table->text('code')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kpis', function (Blueprint $table): void {
            $table->string('code', 50)->unique()->change();
        });
    }
};
