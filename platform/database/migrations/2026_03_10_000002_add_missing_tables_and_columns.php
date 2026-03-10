<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── place_i18n (missing from platform) ──
        Schema::create('place_i18n', function (Blueprint $table) {
            $table->unsignedBigInteger('place_id');
            $table->char('lang', 2);
            $table->string('place_name', 255);
            $table->text('place_desc')->nullable();

            $table->primary(['place_id', 'lang']);
            $table->foreign('place_id')->references('place_id')->on('place')->cascadeOnDelete();
        });

        // ── app_user (demo/test users, missing from platform) ──
        Schema::create('app_user', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('user_code', 50)->nullable()->unique();
            $table->string('display_name', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // ── Align journey_i18n: add missing columns from dump ──
        Schema::table('journey_i18n', function (Blueprint $table) {
            $table->string('journey_name_th_inline', 255)->nullable()->after('lang');
            $table->text('persona_fit_note')->nullable()->after('description');
            $table->text('earn_burn_boost_note')->nullable()->after('persona_fit_note');
            $table->text('luxury_tone_th')->nullable()->after('earn_burn_boost_note');
            $table->text('luxury_tone_en')->nullable()->after('luxury_tone_th');
        });

        // ── Align journey_step: add missing columns from dump ──
        Schema::table('journey_step', function (Blueprint $table) {
            $table->unsignedSmallInteger('travel_minutes')->default(0)->after('step_no');
            $table->unsignedSmallInteger('activity_minutes')->default(0)->after('travel_minutes');
            $table->unsignedInteger('cost_thb_per_person')->default(0)->after('activity_minutes');
            $table->string('tier_code', 10)->default('M')->after('cost_thb_per_person');
            $table->boolean('is_mission')->default(false)->after('tier_code');
            $table->enum('mission_type', ['CHECKIN', 'REVIEW', 'NONE'])->default('NONE')->after('is_mission');
        });

        // ── Align journey_tag: add weight column from dump ──
        Schema::table('journey_tag', function (Blueprint $table) {
            $table->tinyInteger('weight')->default(3)->after('tag_code');
        });

        // ── Align journey: add policy_code from dump ──
        Schema::table('journey', function (Blueprint $table) {
            $table->string('policy_code', 30)->default('DEFAULT_2026')->after('journey_code');
        });
    }

    public function down(): void
    {
        Schema::table('journey', function (Blueprint $table) {
            $table->dropColumn('policy_code');
        });

        Schema::table('journey_tag', function (Blueprint $table) {
            $table->dropColumn('weight');
        });

        Schema::table('journey_step', function (Blueprint $table) {
            $table->dropColumn(['travel_minutes', 'activity_minutes', 'cost_thb_per_person', 'tier_code', 'is_mission', 'mission_type']);
        });

        Schema::table('journey_i18n', function (Blueprint $table) {
            $table->dropColumn(['journey_name_th_inline', 'persona_fit_note', 'earn_burn_boost_note', 'luxury_tone_th', 'luxury_tone_en']);
        });

        Schema::dropIfExists('app_user');
        Schema::dropIfExists('place_i18n');
    }
};
