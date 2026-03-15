import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Rate, Trend } from 'k6/metrics';
import { ssoLogin, authHeaders } from '../helpers/auth.js';
import { randomEmail, randomName, randomMerchantId, randomJourneyCode, randomBookingDate } from '../helpers/data-generators.js';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';

const errorRate = new Rate('errors');
const queryLatency = new Trend('db_query_latency', true);

export const options = {
    scenarios: {
        // Heavy read scenario
        heavy_reads: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '1m', target: 200 },
                { duration: '3m', target: 500 },
                { duration: '3m', target: 1000 },
                { duration: '1m', target: 0 },
            ],
            exec: 'heavyReads',
        },
        // Concurrent writes scenario
        concurrent_writes: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '1m', target: 50 },
                { duration: '3m', target: 200 },
                { duration: '3m', target: 500 },
                { duration: '1m', target: 0 },
            ],
            exec: 'concurrentWrites',
        },
    },
    thresholds: {
        db_query_latency: ['p(95)<500'],
        http_req_failed: ['rate<0.02'],
    },
};

export function setup() {
    const tokens = [];
    for (let i = 0; i < 5; i++) {
        const token = ssoLogin('google', `google-db-stress-${i}`, randomName(), randomEmail());
        if (token) tokens.push(token);
    }
    return { tokens };
}

// Heavy read scenario: hammer search and browse endpoints
export function heavyReads(data) {
    const token = data.tokens[Math.floor(Math.random() * data.tokens.length)];
    const headers = authHeaders(token);

    const endpoint = Math.random();

    if (endpoint < 0.3) {
        // Merchant search (hits vw_merchant_search_public view)
        group('db_merchant_search', () => {
            const start = Date.now();
            const res = http.get(`${BASE_URL}/api/merchants/search?q=spa&limit=50`, { headers });
            queryLatency.add(Date.now() - start);
            check(res, { 'search ok': (r) => r.status === 200 }) || errorRate.add(1);
        });

    } else if (endpoint < 0.6) {
        // Journey one-call (complex view join)
        group('db_journey_onecall', () => {
            const code = randomJourneyCode();
            const start = Date.now();
            const res = http.get(`${BASE_URL}/api/journeys/${code}/onecall/final`, { headers });
            queryLatency.add(Date.now() - start);
            check(res, { 'onecall ok': (r) => r.status === 200 || r.status === 404 }) || errorRate.add(1);
        });

    } else {
        // Discover merchants with filters (N+1 potential)
        group('db_discover_merchants', () => {
            const start = Date.now();
            const res = http.get(`${BASE_URL}/api/mobile/discover/merchants?limit=50&sort=name`, { headers });
            queryLatency.add(Date.now() - start);
            check(res, { 'discover ok': (r) => r.status === 200 }) || errorRate.add(1);
        });
    }

    sleep(Math.random() * 0.5);
}

// Concurrent writes scenario: booking creation deadlock detection
export function concurrentWrites(data) {
    const token = data.tokens[Math.floor(Math.random() * data.tokens.length)];
    const headers = authHeaders(token);

    const writeType = Math.random();

    if (writeType < 0.4) {
        // Concurrent booking creation
        group('db_concurrent_bookings', () => {
            const start = Date.now();
            const res = http.post(`${BASE_URL}/api/mobile/bookings`, JSON.stringify({
                merchant_id: randomMerchantId(),
                booking_date: randomBookingDate(),
                party_size: 2,
                items: [
                    { name: 'Service A', quantity: 1, unit_price: 500 },
                    { name: 'Service B', quantity: 2, unit_price: 300 },
                ],
            }), { headers });
            queryLatency.add(Date.now() - start);
            check(res, { 'booking write ok': (r) => r.status === 201 }) || errorRate.add(1);
        });

    } else if (writeType < 0.7) {
        // Concurrent deal redemptions (unique constraint test)
        group('db_concurrent_redemptions', () => {
            const dealId = Math.floor(Math.random() * 10) + 1;
            const start = Date.now();
            const res = http.post(`${BASE_URL}/api/mobile/deals/${dealId}/redeem`, null, { headers });
            queryLatency.add(Date.now() - start);
            check(res, {
                'redeem response': (r) => r.status === 201 || r.status === 422 || r.status === 404,
            }) || errorRate.add(1);
        });

    } else {
        // Concurrent check-ins
        group('db_concurrent_checkins', () => {
            const start = Date.now();
            const res = http.post(`${BASE_URL}/api/merchant/checkin`, JSON.stringify({
                user_id: Math.floor(Math.random() * 100) + 1,
                merchant_id: randomMerchantId(),
            }), { headers });
            queryLatency.add(Date.now() - start);
            check(res, {
                'checkin response': (r) => r.status === 201 || r.status === 422,
            }) || errorRate.add(1);
        });
    }

    sleep(Math.random() * 0.3);
}
