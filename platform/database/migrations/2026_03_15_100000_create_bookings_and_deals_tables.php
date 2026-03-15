<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('merchant_id');
            $table->string('journey_code', 50)->nullable();
            $table->date('booking_date');
            $table->time('booking_time')->nullable();
            $table->unsignedTinyInteger('party_size')->default(1);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status', 20)->default('upcoming'); // upcoming, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('booking_date');
        });

        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('name', 255);
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamp('created_at')->nullable();

            $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cluster_id');
            $table->unsignedBigInteger('merchant_id')->nullable();
            $table->string('title_en', 255);
            $table->string('title_th', 255)->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_th')->nullable();
            $table->string('category', 50)->nullable();
            $table->string('cover_image_url', 500)->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->string('discount_type', 20)->nullable(); // percentage, fixed
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('max_redemptions')->default(0); // 0 = unlimited
            $table->unsignedInteger('priority')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->index(['cluster_id', 'is_active']);
        });

        Schema::create('deal_redemptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('redeemed_at');
            $table->timestamp('created_at')->nullable();

            $table->unique(['deal_id', 'user_id']);
            $table->foreign('deal_id')->references('id')->on('deals')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_redemptions');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('booking_items');
        Schema::dropIfExists('bookings');
    }
};
