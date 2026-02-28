<?php

namespace App\Services\SSO;

use App\Models\Auth\User;
use App\Models\Global\Cluster;
use App\Services\Cluster\ClusterManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Single Sign-On service.
 * Handles authentication across all clusters and countries.
 * One account works everywhere - access controlled by permissions.
 */
class SsoService
{
    public function __construct(
        private ClusterManager $clusterManager,
    ) {}

    /**
     * Authenticate with email/password.
     */
    public function authenticateWithCredentials(string $email, string $password): ?User
    {
        $user = User::where('email', $email)->active()->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        $this->recordLogin($user);
        return $user;
    }

    /**
     * Authenticate or register via SSO provider (Google, Facebook, LINE, etc.)
     */
    public function authenticateWithProvider(string $provider, string $providerId, array $userData): User
    {
        $user = User::where('sso_provider', $provider)
            ->where('sso_provider_id', $providerId)
            ->first();

        if ($user) {
            $this->recordLogin($user);
            return $user;
        }

        // Check if email already exists (link accounts)
        if (!empty($userData['email'])) {
            $user = User::where('email', $userData['email'])->first();
            if ($user) {
                $user->update([
                    'sso_provider' => $provider,
                    'sso_provider_id' => $providerId,
                ]);
                $this->recordLogin($user);
                return $user;
            }
        }

        // Create new user
        $user = User::create([
            'name' => $userData['name'] ?? 'User',
            'email' => $userData['email'] ?? null,
            'phone' => $userData['phone'] ?? null,
            'avatar' => $userData['avatar'] ?? null,
            'password' => Hash::make(Str::random(32)),
            'sso_provider' => $provider,
            'sso_provider_id' => $providerId,
            'locale' => $userData['locale'] ?? 'th',
            'status' => 'active',
        ]);

        $this->recordLogin($user);
        return $user;
    }

    /**
     * Generate a token for API authentication.
     */
    public function createToken(User $user, string $deviceName = 'default'): string
    {
        // Uses Laravel Sanctum
        return $user->createToken($deviceName)->plainTextToken;
    }

    /**
     * Build the SSO session payload (what the frontend/apps receive).
     */
    public function buildSessionPayload(User $user, ?int $clusterId = null): array
    {
        $payload = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'locale' => $user->locale,
            ],
            'roles' => $user->roles()->get()->map(fn ($r) => [
                'slug' => $r->slug,
                'level' => $r->level,
                'country_id' => $r->pivot->country_id,
                'cluster_id' => $r->pivot->cluster_id,
            ]),
            'groups' => $user->groups()->pluck('slug'),
            'is_global_admin' => $user->isGlobalAdmin(),
        ];

        if ($clusterId) {
            $payload['cluster'] = [
                'id' => $clusterId,
                'accessible_apps' => $user->accessibleAppIds($clusterId),
                'accessible_modules' => $user->accessibleModuleIds($clusterId),
            ];
        }

        return $payload;
    }

    private function recordLogin(User $user): void
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_cluster' => $this->clusterManager->current()?->slug,
        ]);
    }
}
