import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Rate, Counter } from 'k6/metrics';
import { ssoLogin, authHeaders } from '../helpers/auth.js';
import { randomEmail, randomName } from '../helpers/data-generators.js';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';

const errorRate = new Rate('errors');
const circuitBreakerTrips = new Counter('circuit_breaker_trips');

export const options = {
    scenarios: {
        // Stress test: rapid fire requests
        stress: {
            executor: 'constant-arrival-rate',
            rate: 100,              // 100 requests per second
            timeUnit: '1s',
            duration: '5m',
            preAllocatedVUs: 200,
            maxVUs: 500,
        },
    },
    thresholds: {
        http_req_duration: ['p(95)<1500'],
        http_req_failed: ['rate<0.05'],   // allow 5% failure for stress test
        errors: ['rate<0.05'],
    },
};

export function setup() {
    const email = randomEmail();
    const token = ssoLogin('google', 'google-stress-test', randomName(), email);
    return { token };
}

export default function (data) {
    const headers = authHeaders(data.token);

    const scenario = Math.random();

    if (scenario < 0.25) {
        // Payment creation stress
        group('payment_stress', () => {
            const res = http.post(`${BASE_URL}/api/integrations/payment/create`, JSON.stringify({
                amount: Math.floor(Math.random() * 10000) + 100,
                currency: 'THB',
                description: 'Stress test payment',
                method: 'promptpay',
            }), { headers });

            const ok = check(res, {
                'payment response': (r) => r.status === 200 || r.status === 201 || r.status === 503,
            });
            if (!ok) errorRate.add(1);
            if (res.status === 503) circuitBreakerTrips.add(1);
        });

    } else if (scenario < 0.50) {
        // Translation burst
        group('translate_burst', () => {
            const res = http.post(`${BASE_URL}/api/integrations/translate`, JSON.stringify({
                text: 'Welcome to Pattaya! Enjoy your stay.',
                source: 'en',
                target: 'th',
            }), { headers });

            check(res, {
                'translate response': (r) => r.status === 200 || r.status === 503,
            }) || errorRate.add(1);
        });

    } else if (scenario < 0.75) {
        // TTS concurrent requests
        group('tts_concurrent', () => {
            const res = http.post(`${BASE_URL}/api/integrations/tts/synthesize`, JSON.stringify({
                text: 'สวัสดีครับ ยินดีต้อนรับสู่พัทยา',
                voice: 'th-TH-Standard-A',
            }), { headers });

            check(res, {
                'tts response': (r) => r.status === 200 || r.status === 503,
            }) || errorRate.add(1);
        });

    } else {
        // Integration health check flood
        group('health_check_flood', () => {
            const res = http.get(`${BASE_URL}/api/integrations/health`, { headers });

            check(res, {
                'health check ok': (r) => r.status === 200,
            }) || errorRate.add(1);
        });
    }

    sleep(Math.random() * 0.5); // minimal think time for stress test
}
