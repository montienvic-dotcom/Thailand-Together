<?php

namespace App\Services\ApiGateway\Adapters;

/**
 * External authorization adapter for OAuth2/RBAC services.
 * Bridges Thailand Together SSO with external identity providers.
 */
class AuthorizationAdapter extends BaseAdapter
{
    public function providerName(): string
    {
        return $this->config['provider_name'] ?? 'Authorization Service';
    }

    /**
     * Validate an external token.
     */
    public function validateToken(string $token): mixed
    {
        return $this->execute('POST', '/token/validate', [
            'token' => $token,
        ]);
    }

    /**
     * Exchange authorization code for tokens.
     */
    public function exchangeCode(string $code, string $redirectUri): mixed
    {
        return $this->execute('POST', '/token/exchange', [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ]);
    }

    /**
     * Refresh an access token.
     */
    public function refreshToken(string $refreshToken): mixed
    {
        return $this->execute('POST', '/token/refresh', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);
    }

    /**
     * Get user info from external provider.
     */
    public function getUserInfo(string $token): mixed
    {
        return $this->execute('GET', '/userinfo', [
            'access_token' => $token,
        ]);
    }

    /**
     * Revoke a token.
     */
    public function revokeToken(string $token): mixed
    {
        return $this->execute('POST', '/token/revoke', [
            'token' => $token,
        ]);
    }

    protected function baseUrl(): string
    {
        return $this->credential?->credential('base_url')
            ?? $this->config['base_url']
            ?? '';
    }

    protected function headers(): array
    {
        $clientId = $this->credential?->credential('client_id') ?? '';
        $clientSecret = $this->credential?->credential('client_secret') ?? '';

        return [
            'Authorization' => 'Basic ' . base64_encode("{$clientId}:{$clientSecret}"),
            'Content-Type' => 'application/json',
        ];
    }
}
