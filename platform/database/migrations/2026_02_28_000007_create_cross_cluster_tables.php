<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reward points system
        Schema::create('reward_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained()->cascadeOnDelete();
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 10)->default('POINT');
            $table->timestamps();

            $table->unique(['user_id', 'cluster_id', 'currency']);
        });

        Schema::create('reward_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_id')->constrained('reward_wallets')->cascadeOnDelete();
            $table->string('type'); // earn, redeem, transfer_in, transfer_out, exchange
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_type')->nullable(); // booking, purchase, campaign, transfer
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('source_cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->foreignId('target_cluster_id')->nullable()->constrained('clusters')->nullOnDelete();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['wallet_id', 'type']);
        });

        // Exchange rates between clusters for reward points
        Schema::create('reward_exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->foreignId('to_cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->decimal('rate', 10, 4)->default(1.0000); // 1 point from = X points to
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['from_cluster_id', 'to_cluster_id']);
        });

        // Campaigns (global, country-level, or cluster-level)
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('scope'); // global, country, cluster, cross_cluster
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained()->nullOnDelete();
            $table->json('target_clusters')->nullable(); // for cross_cluster: [1,2,5]
            $table->string('type'); // discount, reward_bonus, cross_promotion, exchange_program
            $table->json('rules')->nullable(); // campaign logic/conditions
            $table->json('rewards')->nullable(); // what the campaign gives
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scope', 'is_active']);
            $table->index(['starts_at', 'ends_at']);
        });

        // Tourist exchange recommendations
        Schema::create('cross_cluster_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->foreignId('to_cluster_id')->constrained('clusters')->cascadeOnDelete();
            $table->string('type'); // destination, activity, package, promotion
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('content')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['from_cluster_id', 'is_active']);
        });

        // Global menu configuration
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->foreignId('application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('scope')->default('global'); // global, country, cluster
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained()->nullOnDelete();
            $table->string('target')->default('_self'); // _self, _blank, iframe, spa
            $table->string('visibility')->default('all'); // all, authenticated, guest, admin
            $table->json('required_permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['scope', 'is_active', 'sort_order']);
            $table->index('parent_id');
        });

        // Audit log for cross-cluster operations
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // login, permission_change, data_access, etc.
            $table->string('resource_type')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['resource_type', 'resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('cross_cluster_recommendations');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('reward_exchange_rates');
        Schema::dropIfExists('reward_transactions');
        Schema::dropIfExists('reward_wallets');
    }
};
