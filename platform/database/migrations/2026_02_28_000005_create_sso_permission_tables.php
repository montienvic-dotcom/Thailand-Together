<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify existing users table to add SSO fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('locale', 10)->default('th')->after('avatar');
            $table->string('sso_provider')->nullable()->after('locale'); // google, facebook, line, etc.
            $table->string('sso_provider_id')->nullable()->after('sso_provider');
            $table->string('status')->default('active')->after('sso_provider_id'); // active, suspended, pending
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('last_login_cluster')->nullable()->after('last_login_at');
            $table->softDeletes();

            $table->index('status');
            $table->index(['sso_provider', 'sso_provider_id']);
        });

        // Groups (e.g., #Operators, #Merchants, #Tourists)
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('scope'); // global, country, cluster
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['scope', 'country_id', 'cluster_id']);
        });

        // Roles (e.g., global_admin, country_admin, cluster_admin, app_admin)
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('level'); // global, country, cluster, app
            $table->boolean('is_system')->default(false); // system roles can't be deleted
            $table->timestamps();
        });

        // Permissions (granular actions)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique(); // e.g. "users.create", "bookings.view"
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // grouping for UI
            $table->timestamps();
        });

        // Role has many permissions
        Schema::create('role_permission', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        // User belongs to groups
        Schema::create('group_user', function (Blueprint $table) {
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['group_id', 'user_id']);
            $table->timestamps();
        });

        // User has roles (scoped to country/cluster)
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cluster_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'role_id', 'country_id', 'cluster_id'], 'role_user_unique');
            $table->index(['user_id', 'country_id', 'cluster_id']);
        });

        // User access to specific apps & modules (the fine-grained control)
        // e.g., User A in Group #Client can access App#1 Module 1,2,4
        Schema::create('user_app_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->boolean('has_access')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'cluster_id', 'application_id'], 'user_app_cluster_unique');
        });

        // User access to specific modules within an app
        Schema::create('user_module_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->boolean('has_access')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'cluster_id', 'module_id'], 'user_module_cluster_unique');
        });

        // Group-level app/module access (default for all members)
        Schema::create('group_app_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->boolean('has_access')->default(true);
            $table->timestamps();

            $table->unique(['group_id', 'cluster_id', 'application_id'], 'group_app_cluster_unique');
        });

        Schema::create('group_module_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cluster_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->boolean('has_access')->default(true);
            $table->timestamps();

            $table->unique(['group_id', 'cluster_id', 'module_id'], 'group_module_cluster_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_module_access');
        Schema::dropIfExists('group_app_access');
        Schema::dropIfExists('user_module_access');
        Schema::dropIfExists('user_app_access');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('group_user');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('groups');

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'phone', 'avatar', 'locale', 'sso_provider',
                'sso_provider_id', 'status', 'last_login_at', 'last_login_cluster',
            ]);
        });
    }
};
