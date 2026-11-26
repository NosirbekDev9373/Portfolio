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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('group', ['backend', 'frontend', 'database', 'tools', 'testing']);
            $table->enum('status', ['used', 'learning'])->default('used');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['name', 'group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
