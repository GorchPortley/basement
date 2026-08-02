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
        Schema::table('designs', function (Blueprint $table) {
            $table->boolean('active')->after('owner_id');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('active')->after('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designs', function (Blueprint $table) {
            $table->boolean('active');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->boolean('active');
        });
    }
};
