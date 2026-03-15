import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Rate, Trend } from 'k6/metrics';
import { ssoLogin, authHeaders } from '../helpers/auth.js';
import { randomEmail, randomName, randomCoordinates, randomBookingDate, randomMerchantId, randomJourneyCode, randomPartySize } from '../helpers/data-generators.js';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';

// Custom metrics
const errorRate = new Rate('errors');
const discoverLatency = new Trend('discover_latency', true);
const bookingLatency = new Trend('booking_latency', true);

// Load test configuration
export const options = {
    scenarios: {
        tourist_peak: {
            executor: 'ramping-vus',
            startVUs: 0,
            stages: [
                { duration: '2m', target: 100 },    // ramp-up
                { duration: '5m', target: 500 },    // sustained moderate
                { duration: '5m', target: 1000 },   // sustained high
                { duration: '3m', target: 5000 },   // peak
                { duration: '5m', target: 5000 },   // sustained peak
                { duration: '2m', target: 0 },      // ramp-down
            ],
        },
    },
    thresholds: {
        http_req_duration: ['p(50)<200', 'p(95)<500', 'p(99)<1000'],
        http_req_failed: ['rate<0.01'],
        errors: ['rate<0.01'],
        discover_latency: ['p(95)<400'],
        booking_latency: ['p(95)<800'],
    },
};

export function setup() {
    // Create a pool of test tokens during setup
    const tokens = [];
    for (let i = 0; i < 10; i++) {
        const email = randomEmail();
        const name = randomName();
        const token = ssoLogin('google', `google-loadtest-${i}`, name, email);
        if (token) tokens.push(token);
    }
    return { tokens };
}

export default function (data) {
    const token = data.tokens[Math.floor(Math.random() * data.tokens.length)];
    if (!token) {
        errorRate.add(1);
        return;
    }

    const headers = authHeaders(token);

    // Step 1: Home screen
    group('01_home_screen', () => {
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/mobile/discover/home`, { headers });
        discoverLatency.add(Date.now() - start);

        check(res, {
            'home status 200': (r) => r.status === 200,
            'home has data': (r) => JSON.parse(r.body).data !== undefined,
        }) || errorRate.add(1);
    });
    sleep(Math.random() * 3 + 1);

    // Step 2: Browse journeys
    group('02_browse_journeys', () => {
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/mobile/discover/journeys?limit=20&sort=popular`, { headers });
        discoverLatency.add(Date.now() - start);

        check(res, {
            'journeys status 200': (r) => r.status === 200,
            'journeys has meta': (r) => JSON.parse(r.body).meta !== undefined,
        }) || errorRate.add(1);
    });
    sleep(Math.random() * 4 + 2);

    // Step 3: Journey detail
    group('03_journey_detail', () => {
        const code = randomJourneyCode();
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/mobile/discover/journeys/${code}`, { headers });
        discoverLatency.add(Date.now() - start);

        check(res, {
            'journey detail status ok': (r) => r.status === 200 || r.status === 404,
        }) || errorRate.add(1);
    });
    sleep(Math.random() * 3 + 1);

    // Step 4: Nearby merchants
    group('04_nearby_merchants', () => {
        const coords = randomCoordinates();
        const start = Date.now();
        const res = http.get(
            `${BASE_URL}/api/mobile/discover/merchants/nearby?lat=${coords.lat}&lng=${coords.lng}&radius=5`,
            { headers }
        );
        discoverLatency.add(Date.now() - start);

        check(res, {
            'nearby status 200': (r) => r.status === 200,
            'nearby has meta': (r) => JSON.parse(r.body).meta !== undefined,
        }) || errorRate.add(1);
    });
    sleep(Math.random() * 2 + 1);

    // Step 5: Merchant detail
    group('05_merchant_detail', () => {
        const merchantId = randomMerchantId();
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/mobile/discover/merchants/${merchantId}`, { headers });
        discoverLatency.add(Date.now() - start);

        check(res, {
            'merchant detail status ok': (r) => r.status === 200 || r.status === 404,
        }) || errorRate.add(1);
    });
    sleep(Math.random() * 4 + 2);

    // Step 6: Create booking (30% of users)
    if (Math.random() < 0.3) {
        group('06_create_booking', () => {
            const start = Date.now();
            const res = http.post(`${BASE_URL}/api/mobile/bookings`, JSON.stringify({
                merchant_id: randomMerchantId(),
                journey_code: randomJourneyCode(),
                booking_date: randomBookingDate(),
                party_size: randomPartySize(),
                notes: 'Load test booking',
            }), { headers });
            bookingLatency.add(Date.now() - start);

            check(res, {
                'booking created': (r) => r.status === 201,
            }) || errorRate.add(1);
        });
        sleep(Math.random() * 8 + 5);
    }

    // Step 7: Browse deals
    group('07_browse_deals', () => {
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/mobile/deals`, { headers });
        discoverLatency.add(Date.now() - start);

        check(res, {
            'deals status 200': (r) => r.status === 200,
        }) || errorRate.add(1);
    });
    sleep(Math.random() * 3 + 1);

    // Step 8: Recommendations
    group('08_recommendations', () => {
        const start = Date.now();
        const res = http.get(`${BASE_URL}/api/mobile/discover/recommendations`, { headers });
        discoverLatency.add(Date.now() - start);

        check(res, {
            'recommendations status 200': (r) => r.status === 200,
        }) || errorRate.add(1);
    });
    sleep(Math.random() * 3 + 1);
}

export function teardown(data) {
    // Cleanup: could delete test bookings here
}
