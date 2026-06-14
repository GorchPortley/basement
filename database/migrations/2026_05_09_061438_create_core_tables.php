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
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->morphs('owner');
            $table->json('payload')->nullable();
        });
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->morphs('owner');
            $table->json('payload')->nullable();
        });
        Schema::create('component_design', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('design_id')->constrained();
            $table->foreignId('component_id')->constrained();
            $table->json('payload')->nullable();
        });
        Schema::create('collaborator_design', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->morphs('collaborator');
            $table->foreignId('design_id')->constrained();
            $table->json('payload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designs');
        Schema::dropIfExists('components');
        Schema::dropIfExists('components_designs');
        Schema::dropIfExists('designs_users');
    }
};
