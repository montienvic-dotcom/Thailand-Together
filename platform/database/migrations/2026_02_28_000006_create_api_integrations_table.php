<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // API Provider registry (Payment, SMS, AI, etc.)
        Schema::create('api_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Stripe", "Twilio"
            $table->string('slug')->unique();
            $table->string('category'); // payment, sms, ai, translate, tts, helpdesk, data_exchange, authorization
            $table->text('description')->nullable();
            $table->string('base_url')->nullable();
            $table->string('docs_url')->nullable();
            $table->string('adapter_class')->nullable(); // App\Services\ApiGateway\Adapters\StripeAdapter
            $table->boolean('is_active')->default(true);
            $table->boolean('is_shared')->default(true); // shared across all clusters
            $table->json('supported_countries')->nullable(); // null = all countries
            $table->json('default_config')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('category');
        });

        // Credentials per cluster (or global)
        Schema::create('api_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained()->nullOnDelete();
            $table->string('environment')->default('sandbox'); // sandbox, production
            $table->json('credentials')->nullable(); // encrypted JSON: api_key, secret, etc.
            $table->json('config')->nullable(); // provider-specific config overrides
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['api_provider_id', 'environment']);
            $table->index(['country_id', 'cluster_id']);
        });

        // API call logs for auditing and rate limiting
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('api_provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 10); // GET, POST, etc.
            $table->string('endpoint');
            $table->integer('status_code')->nullable();
            $table->integer('response_time_ms')->nullable(); // response time in ms
            $table->json('request_summary')->nullable(); // sanitized request info
            $table->text('error_message')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['api_provider_id', 'created_at']);
            $table->index(['cluster_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('api_credentials');
        Schema::dropIfExists('api_providers');
    }
};
