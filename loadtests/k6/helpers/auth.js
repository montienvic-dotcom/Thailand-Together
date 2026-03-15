import http from 'k6/http';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';

/**
 * Login via email/password and return auth token.
 */
export function login(email, password) {
    const res = http.post(`${BASE_URL}/api/auth/login`, JSON.stringify({
        email: email,
        password: password,
    }), {
        headers: { 'Content-Type': 'application/json' },
    });

    if (res.status === 200) {
        const body = JSON.parse(res.body);
        return body.token;
    }
    return null;
}

/**
 * Login via SSO provider and return auth token.
 */
export function ssoLogin(provider, providerId, name, email) {
    const res = http.post(`${BASE_URL}/api/auth/sso`, JSON.stringify({
        provider: provider,
        provider_id: providerId,
        name: name,
        email: email,
    }), {
        headers: { 'Content-Type': 'application/json' },
    });

    if (res.status === 200) {
        const body = JSON.parse(res.body);
        return body.token;
    }
    return null;
}

/**
 * Build authenticated headers.
 */
export function authHeaders(token, clusterId = 1) {
    return {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'X-Cluster-Id': String(clusterId),
    };
}
