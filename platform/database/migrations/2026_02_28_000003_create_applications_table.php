<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "App Together", "Hotel Management"
            $table->string('slug')->unique(); // e.g. "app-together"
            $table->string('code', 20)->unique(); // e.g. "APP_TOGETHER"
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 7)->nullable(); // hex color
            $table->string('type')->default('web'); // web, mobile, api, hybrid
            $table->string('base_url')->nullable(); // where the app lives
            $table->string('source')->default('internal'); // internal, external, third-party
            $table->string('source_version')->nullable(); // External source version tracking
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_menu')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot: which clusters have which apps enabled
        Schema::create('cluster_application', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cluster_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->json('config_overrides')->nullable(); // per-cluster app config
            $table->timestamps();

            $table->unique(['cluster_id', 'application_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cluster_application');
        Schema::dropIfExists('applications');
    }
};
