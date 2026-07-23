<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Core marketplace tables.
 *
 * Hybrid storage strategy: the fields we browse / filter / sort by are promoted
 * to real, indexed columns; everything flexible or rarely-queried (bill of
 * materials, driver T/S parameters, free-form spec key-values) lives in the
 * `payload` JSON column. Enum-like fields are stored as plain strings and cast
 * to PHP enums on the model — sqlite has no real ENUM type and the old codebase
 * had to migrate away from DB enums, so we skip that trap from the start.
 *
 * Files & images are NOT stored here — they are attached via Spatie Media
 * Library (see the media table migration) as named collections on each model.
 */
return new class extends Migration
{
    public function up(): void
    {
        // A published speaker design (the "product").
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');                    // owner_id + owner_type (User for now, org later)
            $table->string('name')->index();
            $table->string('slug')->unique();
            $table->string('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('category')->nullable()->index();   // App\Enums\DesignCategory
            $table->string('access')->default('free')->index(); // App\Enums\DesignAccess: free|tip|gated
            $table->decimal('price', 8, 2)->default(0);
            $table->decimal('build_cost', 8, 2)->nullable();
            $table->unsignedTinyInteger('impedance')->nullable(); // nominal ohms
            $table->unsignedInteger('power')->nullable();         // watts
            $table->boolean('official')->default(false)->index(); // manufacturer/verified stamp (never paid ranking)
            $table->boolean('active')->default(false)->index();   // published & publicly listed
            $table->string('forum_slug')->nullable();             // linked Flarum discussion
            $table->json('payload')->nullable();                  // bill_of_materials, extra specs, misc
            $table->timestamps();
        });

        // A driver / part that designs reference (a verifiable driver database).
        Schema::create('components', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->string('brand')->index();
            $table->string('model')->index();
            $table->string('slug')->unique();
            $table->string('category')->nullable()->index();   // App\Enums\ComponentCategory
            $table->decimal('size', 5, 2)->nullable();         // nominal diameter (inches), e.g. 6.50
            $table->string('impedance')->nullable();           // e.g. "4", "8", "2x4" — kept as string like the source
            $table->unsignedInteger('power')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('link')->nullable();                // outbound / affiliate purchase link
            $table->string('summary')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('official')->default(false)->index(); // manufacturer-verified profile
            $table->boolean('active')->default(false)->index();
            $table->string('forum_slug')->nullable();
            $table->json('payload')->nullable();               // factory_specs / Thiele-Small parameters, misc
            $table->timestamps();
        });

        // The "recipe": which components a design uses and how they're deployed.
        Schema::create('component_design', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->foreignId('component_id')->constrained()->cascadeOnDelete();
            $table->string('position')->nullable();            // App\Enums\ComponentPosition: LF|LMF|MF|HMF|HF|Other
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('low_frequency', 10, 2)->nullable();  // crossover low corner (Hz)
            $table->decimal('high_frequency', 10, 2)->nullable(); // crossover high corner (Hz)
            $table->decimal('air_volume', 8, 2)->nullable();      // enclosure volume for this driver (litres)
            $table->json('payload')->nullable();                  // per-placement measured specs
            $table->timestamps();
        });

        // Additional people credited on / permitted to edit a design.
        Schema::create('collaborator_design', function (Blueprint $table) {
            $table->id();
            $table->morphs('collaborator');                    // usually a User
            $table->foreignId('design_id')->constrained()->cascadeOnDelete();
            $table->json('payload')->nullable();               // role, permissions, credit note
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collaborator_design');
        Schema::dropIfExists('component_design');
        Schema::dropIfExists('components');
        Schema::dropIfExists('designs');
    }
};
