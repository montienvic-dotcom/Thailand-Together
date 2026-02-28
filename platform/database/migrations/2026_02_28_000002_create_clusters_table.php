<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clusters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g. "Pattaya"
            $table->string('slug')->unique(); // e.g. "pattaya"
            $table->string('code', 10)->unique(); // e.g. "PTY"
            $table->text('description')->nullable();
            $table->string('timezone')->nullable(); // override country timezone
            $table->string('default_locale', 10)->nullable(); // override country locale
            $table->json('settings')->nullable(); // cluster-specific config
            $table->string('database_connection')->nullable(); // for data isolation
            $table->boolean('is_active')->default(false);
            $table->date('launch_date')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['country_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clusters');
    }
};
