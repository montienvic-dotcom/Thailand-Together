<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 3)->unique(); // ISO 3166-1 alpha-3
            $table->string('code_alpha2', 2)->unique(); // ISO 3166-1 alpha-2
            $table->string('currency_code', 3)->default('THB');
            $table->string('timezone')->default('Asia/Bangkok');
            $table->string('default_locale', 10)->default('th');
            $table->json('supported_locales')->nullable();
            $table->boolean('is_active')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
