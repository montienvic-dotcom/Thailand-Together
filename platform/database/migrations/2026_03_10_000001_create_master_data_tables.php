<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── md_country ──
        Schema::create('md_country', function (Blueprint $table) {
            $table->string('country_code', 10)->primary();
            $table->string('country_name_th', 100);
            $table->string('country_name_en', 100);
            $table->string('continent', 50);
        });

        // ── md_market_zone ──
        Schema::create('md_market_zone', function (Blueprint $table) {
            $table->string('zone_code', 30)->primary();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
        });

        // ── md_market_zone_i18n ──
        Schema::create('md_market_zone_i18n', function (Blueprint $table) {
            $table->string('zone_code', 30);
            $table->string('lang', 10);
            $table->string('zone_name', 255);
            $table->text('zone_desc')->nullable();

            $table->primary(['zone_code', 'lang']);
            $table->foreign('zone_code')->references('zone_code')->on('md_market_zone')->cascadeOnDelete();
        });

        // ── md_partner_tier ──
        Schema::create('md_partner_tier', function (Blueprint $table) {
            $table->string('tier_code', 10)->primary();
            $table->string('tier_name_th', 100);
            $table->string('tier_name_en', 100);
        });

        // ── md_persona ──
        Schema::create('md_persona', function (Blueprint $table) {
            $table->string('persona_code', 10)->primary();
            $table->string('persona_name_th', 200);
            $table->string('persona_name_en', 200);
        });

        // ── md_persona_i18n ──
        Schema::create('md_persona_i18n', function (Blueprint $table) {
            $table->string('persona_code', 10);
            $table->char('lang', 2);
            $table->string('persona_name', 200);

            $table->primary(['persona_code', 'lang']);
            $table->foreign('persona_code')->references('persona_code')->on('md_persona')->cascadeOnDelete();
        });

        // ── md_point_policy ──
        Schema::create('md_point_policy', function (Blueprint $table) {
            $table->string('policy_code', 30)->primary();
            $table->integer('normal_divisor')->default(25);
            $table->decimal('goal_multiplier', 6, 3)->default(1.300);
            $table->decimal('special_multiplier', 6, 3)->default(2.000);
            $table->integer('mission_checkin_normal')->default(10);
            $table->integer('mission_checkin_goal')->default(20);
            $table->integer('mission_checkin_special')->default(50);
            $table->integer('mission_review_normal')->default(20);
            $table->integer('mission_review_goal')->default(40);
            $table->integer('mission_review_special')->default(80);
        });

        // ── md_tag ──
        Schema::create('md_tag', function (Blueprint $table) {
            $table->integer('tag_id')->primary();
            $table->string('tag_code', 50)->unique();
            $table->boolean('is_active')->default(true);
        });

        // ── md_tag_i18n ──
        Schema::create('md_tag_i18n', function (Blueprint $table) {
            $table->integer('tag_id');
            $table->char('lang', 2);
            $table->string('tag_name', 100);

            $table->primary(['tag_id', 'lang']);
            $table->foreign('tag_id')->references('tag_id')->on('md_tag')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('md_tag_i18n');
        Schema::dropIfExists('md_tag');
        Schema::dropIfExists('md_point_policy');
        Schema::dropIfExists('md_persona_i18n');
        Schema::dropIfExists('md_persona');
        Schema::dropIfExists('md_partner_tier');
        Schema::dropIfExists('md_market_zone_i18n');
        Schema::dropIfExists('md_market_zone');
        Schema::dropIfExists('md_country');
    }
};
