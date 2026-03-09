<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Journey ──
        Schema::create('journey', function (Blueprint $table) {
            $table->id('journey_id');
            $table->string('journey_code', 10)->unique();
            $table->string('journey_group', 5)->comment('A,B,C,...H');
            $table->string('journey_name_th');
            $table->string('journey_name_en');
            $table->unsignedTinyInteger('group_size')->default(4);
            $table->decimal('gmv_per_person', 10, 2)->default(0);
            $table->decimal('gmv_per_group', 10, 2)->default(0);
            $table->unsignedSmallInteger('tp_total_normal')->default(0);
            $table->unsignedSmallInteger('tp_total_goal')->default(0);
            $table->unsignedSmallInteger('tp_total_special')->default(0);
            $table->unsignedSmallInteger('total_minutes_sum')->default(0);
            $table->string('luxury_tone_th')->nullable();
            $table->string('luxury_tone_en')->nullable();
            $table->string('target_visitors')->nullable();
            $table->string('status', 20)->default('ACTIVE');
            $table->unsignedBigInteger('cluster_id')->nullable();
            $table->timestamps();

            $table->index('journey_group');
            $table->index('cluster_id');
        });

        // ── Journey I18n ──
        Schema::create('journey_i18n', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journey_id');
            $table->string('lang', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['journey_id', 'lang']);
            $table->foreign('journey_id')->references('journey_id')->on('journey')->cascadeOnDelete();
        });

        // ── Journey Tags ──
        Schema::create('journey_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journey_id');
            $table->string('tag_code', 50);

            $table->unique(['journey_id', 'tag_code']);
            $table->foreign('journey_id')->references('journey_id')->on('journey')->cascadeOnDelete();
        });

        // ── Journey Personas ──
        Schema::create('journey_persona', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journey_id');
            $table->string('persona_code', 30);

            $table->unique(['journey_id', 'persona_code']);
            $table->foreign('journey_id')->references('journey_id')->on('journey')->cascadeOnDelete();
        });

        // ── Journey Markets ──
        Schema::create('journey_market', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journey_id');
            $table->string('country_code', 5);
            $table->unsignedTinyInteger('fit_level')->default(3);

            $table->unique(['journey_id', 'country_code']);
            $table->foreign('journey_id')->references('journey_id')->on('journey')->cascadeOnDelete();
        });

        // ── Journey Zones ──
        Schema::create('journey_zone', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journey_id');
            $table->string('zone_code', 30);
            $table->unsignedTinyInteger('fit_level')->default(3);

            $table->unique(['journey_id', 'zone_code']);
            $table->foreign('journey_id')->references('journey_id')->on('journey')->cascadeOnDelete();
        });

        // ── Journey Next5 ──
        Schema::create('journey_next5', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journey_id');
            $table->unsignedTinyInteger('next_rank');
            $table->string('next_journey_code', 10);

            $table->unique(['journey_id', 'next_rank']);
            $table->foreign('journey_id')->references('journey_id')->on('journey')->cascadeOnDelete();
        });

        // ── Place ──
        Schema::create('place', function (Blueprint $table) {
            $table->id('place_id');
            $table->string('place_code', 80)->unique();
            $table->string('place_name_th');
            $table->string('place_name_en');
            $table->text('place_desc_th')->nullable();
            $table->text('place_desc_en')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('place_type', 30)->nullable()->comment('restaurant,hotel,spa,attraction,...');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('cluster_id')->nullable();
            $table->timestamps();

            $table->index('cluster_id');
        });

        // ── Journey Step (links journey → place) ──
        Schema::create('journey_step', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journey_id');
            $table->unsignedBigInteger('place_id');
            $table->unsignedTinyInteger('step_no');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->unsignedSmallInteger('tp_normal')->default(0);
            $table->unsignedSmallInteger('tp_goal')->default(0);
            $table->unsignedSmallInteger('tp_special')->default(0);
            $table->decimal('spend_estimate', 10, 2)->default(0);
            $table->text('step_note')->nullable();

            $table->unique(['journey_id', 'step_no']);
            $table->foreign('journey_id')->references('journey_id')->on('journey')->cascadeOnDelete();
            $table->foreign('place_id')->references('place_id')->on('place')->cascadeOnDelete();
        });

        // ── Merchant ──
        Schema::create('merchant', function (Blueprint $table) {
            $table->id('merchant_id');
            $table->string('merchant_code', 80)->unique();
            $table->string('merchant_name_th');
            $table->string('merchant_name_en');
            $table->text('merchant_desc_th')->nullable();
            $table->text('merchant_desc_en')->nullable();
            $table->string('default_tier_code', 5)->default('S')->comment('XL,E,M,S');
            $table->boolean('is_active')->default(true);
            $table->string('phone', 30)->nullable();
            $table->string('website')->nullable();
            $table->unsignedTinyInteger('price_level')->default(2)->comment('1-5');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('open_hours', 50)->nullable();
            $table->string('service_tags')->nullable()->comment('comma-separated');
            $table->string('onsite_note')->nullable();
            $table->string('source_ref')->nullable()->comment('import source reference');
            $table->unsignedBigInteger('cluster_id')->nullable();
            $table->timestamps();

            $table->index('default_tier_code');
            $table->index('cluster_id');
        });

        // ── Merchant I18n ──
        Schema::create('merchant_i18n', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id');
            $table->string('lang', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['merchant_id', 'lang']);
            $table->foreign('merchant_id')->references('merchant_id')->on('merchant')->cascadeOnDelete();
        });

        // ── Place ↔ Merchant mapping ──
        Schema::create('place_merchant', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('place_id');
            $table->unsignedBigInteger('merchant_id');
            $table->boolean('is_primary')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['place_id', 'merchant_id']);
            $table->foreign('place_id')->references('place_id')->on('place')->cascadeOnDelete();
            $table->foreign('merchant_id')->references('merchant_id')->on('merchant')->cascadeOnDelete();
        });

        // ── Merchant Check-in ──
        Schema::create('merchant_checkin', function (Blueprint $table) {
            $table->id('checkin_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('place_id');
            $table->unsignedBigInteger('journey_id')->nullable();
            $table->string('checkin_method', 10)->default('QR');
            $table->string('note')->nullable();
            $table->unsignedSmallInteger('tp_awarded')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'merchant_id']);
            $table->index('journey_id');
            $table->foreign('merchant_id')->references('merchant_id')->on('merchant')->cascadeOnDelete();
            $table->foreign('place_id')->references('place_id')->on('place')->cascadeOnDelete();
            $table->foreign('journey_id')->references('journey_id')->on('journey')->nullOnDelete();
        });

        // ── Merchant Favorite ──
        Schema::create('merchant_favorite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('merchant_id');
            $table->timestamps();

            $table->unique(['user_id', 'merchant_id']);
            $table->foreign('merchant_id')->references('merchant_id')->on('merchant')->cascadeOnDelete();
        });

        // ── Merchant Wishlist ──
        Schema::create('merchant_wishlist', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('merchant_id');
            $table->timestamps();

            $table->unique(['user_id', 'merchant_id']);
            $table->foreign('merchant_id')->references('merchant_id')->on('merchant')->cascadeOnDelete();
        });

        // ── Merchant Review ──
        Schema::create('merchant_review', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('merchant_id');
            $table->unsignedBigInteger('place_id')->nullable();
            $table->unsignedBigInteger('journey_id')->nullable();
            $table->unsignedTinyInteger('rating')->comment('1-5');
            $table->string('title')->nullable();
            $table->text('review_text')->nullable();
            $table->string('status', 20)->default('PUBLISHED');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['merchant_id', 'status', 'is_public']);
            $table->index('user_id');
            $table->foreign('merchant_id')->references('merchant_id')->on('merchant')->cascadeOnDelete();
            $table->foreign('place_id')->references('place_id')->on('place')->nullOnDelete();
            $table->foreign('journey_id')->references('journey_id')->on('journey')->nullOnDelete();
        });

        // ── Merchant Import Staging ──
        Schema::create('merchant_import_batch', function (Blueprint $table) {
            $table->id('batch_id');
            $table->string('batch_code', 50)->unique();
            $table->string('batch_label')->nullable();
            $table->string('status', 20)->default('PENDING');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->unsignedInteger('error_rows')->default(0);
            $table->timestamps();
        });

        Schema::create('stg_merchant_import', function (Blueprint $table) {
            $table->id();
            $table->string('batch_code', 50);
            $table->string('merchant_code', 80);
            $table->string('merchant_name_th');
            $table->string('merchant_name_en')->nullable();
            $table->text('merchant_desc_th')->nullable();
            $table->text('merchant_desc_en')->nullable();
            $table->string('default_tier_code', 5)->default('S');
            $table->boolean('is_active')->default(true);
            $table->string('phone', 30)->nullable();
            $table->string('website')->nullable();
            $table->unsignedTinyInteger('price_level')->default(2);
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('place_code', 80)->nullable();
            $table->boolean('is_primary_hint')->default(false);
            $table->string('onsite_note')->nullable();
            $table->string('open_hours', 50)->nullable();
            $table->string('service_tags')->nullable();
            $table->string('source_ref')->nullable();
            $table->string('validation_status', 20)->default('PENDING');
            $table->text('validation_errors')->nullable();
            $table->timestamps();

            $table->index('batch_code');
            $table->index('merchant_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stg_merchant_import');
        Schema::dropIfExists('merchant_import_batch');
        Schema::dropIfExists('merchant_review');
        Schema::dropIfExists('merchant_wishlist');
        Schema::dropIfExists('merchant_favorite');
        Schema::dropIfExists('merchant_checkin');
        Schema::dropIfExists('place_merchant');
        Schema::dropIfExists('merchant_i18n');
        Schema::dropIfExists('merchant');
        Schema::dropIfExists('journey_step');
        Schema::dropIfExists('place');
        Schema::dropIfExists('journey_next5');
        Schema::dropIfExists('journey_zone');
        Schema::dropIfExists('journey_market');
        Schema::dropIfExists('journey_persona');
        Schema::dropIfExists('journey_tag');
        Schema::dropIfExists('journey_i18n');
        Schema::dropIfExists('journey');
    }
};
