<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Booking Module"
            $table->string('slug'); // e.g. "booking"
            $table->string('code', 30); // e.g. "BOOKING"
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('route_prefix')->nullable(); // URL prefix for this module
            $table->boolean('is_active')->default(true);
            $table->boolean('is_premium')->default(false); // premium module flag
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['application_id', 'slug']);
            $table->unique(['application_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
