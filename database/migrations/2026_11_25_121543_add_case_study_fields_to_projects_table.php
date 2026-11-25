<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('problem_uz')->nullable();
            $table->text('problem_en')->nullable();
            $table->text('solution_uz')->nullable();
            $table->text('solution_en')->nullable();
            $table->json('features')->nullable();
            $table->json('contributions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'contributions',
                'features',
                'solution_en',
                'solution_uz',
                'problem_en',
                'problem_uz',
            ]);
        });
    }
};
