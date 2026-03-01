<?php

namespace App\Models\Auth;

use App\Models\App\Application;
use App\Models\App\Module;
use App\Models\Global\Cluster;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar', 'locale',
        'sso_provider', 'sso_provider_id', 'status',
        'last_login_at', 'last_login_cluster',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ── Relationships ──

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'group_user')->withTimestamps();
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot(['country_id', 'cluster_id'])
            ->withTimestamps();
    }

    // ── Role Checks ──

    public function isGlobalAdmin(): bool
    {
        return $this->roles()->where('slug', 'global-admin')->exists();
    }

    public function isCountryAdmin(int $countryId): bool
    {
        return $this->isGlobalAdmin()
            || $this->roles()
                ->where('slug', 'country-admin')
                ->wherePivot('country_id', $countryId)
                ->exists();
    }

    public function isClusterAdmin(int $clusterId): bool
    {
        $cluster = Cluster::find($clusterId);
        if (!$cluster) {
            return false;
        }

        return $this->isCountryAdmin($cluster->country_id)
            || $this->roles()
                ->where('slug', 'cluster-admin')
                ->wherePivot('cluster_id', $clusterId)
                ->exists();
    }

    // ── App/Module Access ──

    /**
     * Check if user can access a specific app in a specific cluster.
     * Resolution order: user-specific > group-level > role-level
     */
    public function canAccessApp(int $applicationId, int $clusterId): bool
    {
        // Global/Country/Cluster admins have full access
        $cluster = Cluster::find($clusterId);
        if ($cluster && $this->isClusterAdmin($clusterId)) {
            return true;
        }

        // Check user-specific access
        $userAccess = \Illuminate\Support\Facades\DB::table('user_app_access')
            ->where('user_id', $this->id)
            ->where('cluster_id', $clusterId)
            ->where('application_id', $applicationId)
            ->first();

        if ($userAccess) {
            return (bool) $userAccess->has_access;
        }

        // Check group-level access
        $groupIds = $this->groups()->pluck('groups.id');
        if ($groupIds->isNotEmpty()) {
            $groupAccess = \Illuminate\Support\Facades\DB::table('group_app_access')
                ->whereIn('group_id', $groupIds)
                ->where('cluster_id', $clusterId)
                ->where('application_id', $applicationId)
                ->where('has_access', true)
                ->exists();

            if ($groupAccess) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can access a specific module in a specific cluster.
     */
    public function canAccessModule(int $moduleId, int $clusterId): bool
    {
        $module = Module::find($moduleId);
        if (!$module) {
            return false;
        }

        // Must have app access first
        if (!$this->canAccessApp($module->application_id, $clusterId)) {
            return false;
        }

        // Admins have full module access
        if ($this->isClusterAdmin($clusterId)) {
            return true;
        }

        // Check user-specific module access
        $userAccess = \Illuminate\Support\Facades\DB::table('user_module_access')
            ->where('user_id', $this->id)
            ->where('cluster_id', $clusterId)
            ->where('module_id', $moduleId)
            ->first();

        if ($userAccess) {
            return (bool) $userAccess->has_access;
        }

        // Check group-level module access
        $groupIds = $this->groups()->pluck('groups.id');
        if ($groupIds->isNotEmpty()) {
            return \Illuminate\Support\Facades\DB::table('group_module_access')
                ->whereIn('group_id', $groupIds)
                ->where('cluster_id', $clusterId)
                ->where('module_id', $moduleId)
                ->where('has_access', true)
                ->exists();
        }

        return false;
    }

    /**
     * Get all accessible app IDs for a user in a specific cluster.
     */
    public function accessibleAppIds(int $clusterId): array
    {
        if ($this->isClusterAdmin($clusterId)) {
            return Application::active()->pluck('id')->toArray();
        }

        // User-specific
        $userApps = \Illuminate\Support\Facades\DB::table('user_app_access')
            ->where('user_id', $this->id)
            ->where('cluster_id', $clusterId)
            ->where('has_access', true)
            ->pluck('application_id');

        // Group-level
        $groupIds = $this->groups()->pluck('groups.id');
        $groupApps = collect();
        if ($groupIds->isNotEmpty()) {
            $groupApps = \Illuminate\Support\Facades\DB::table('group_app_access')
                ->whereIn('group_id', $groupIds)
                ->where('cluster_id', $clusterId)
                ->where('has_access', true)
                ->pluck('application_id');
        }

        return $userApps->merge($groupApps)->unique()->values()->toArray();
    }

    /**
     * Get all accessible module IDs for a user in a specific cluster.
     */
    public function accessibleModuleIds(int $clusterId): array
    {
        if ($this->isClusterAdmin($clusterId)) {
            return Module::active()->pluck('id')->toArray();
        }

        $userModules = \Illuminate\Support\Facades\DB::table('user_module_access')
            ->where('user_id', $this->id)
            ->where('cluster_id', $clusterId)
            ->where('has_access', true)
            ->pluck('module_id');

        $groupIds = $this->groups()->pluck('groups.id');
        $groupModules = collect();
        if ($groupIds->isNotEmpty()) {
            $groupModules = \Illuminate\Support\Facades\DB::table('group_module_access')
                ->whereIn('group_id', $groupIds)
                ->where('cluster_id', $clusterId)
                ->where('has_access', true)
                ->pluck('module_id');
        }

        return $userModules->merge($groupModules)->unique()->values()->toArray();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
