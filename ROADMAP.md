# Thailand Together — Platform Roadmap

## Phase 1: Pattaya Foundation (Year 1)

### Q1 — Core Platform ✅
- [x] Laravel multi-tenant architecture
- [x] SSO & 5-level permission system
- [x] Global Header Menu (Super App Shell)
- [x] API Integration Layer (9 categories)
- [x] Patch Management System
- [x] Backend Admin System (Clusters, Users, Roles, Groups, Apps, API Providers)
- [x] Pattaya cluster setup & configuration
- [x] Docker containerization (global + cluster template)

### Q2 — Mobile & Integration
- [x] Mobile API: Discover (journeys, merchants, nearby, recommendations)
- [x] Mobile API: Bookings (create, list, cancel with line items)
- [x] Mobile API: Deals & Promotions (browse, redeem)
- [x] Mobile API: Profile, Notifications, Device tokens
- [x] Cross-cluster reward system & exchange rates
- [x] External App Adapter Interface
- [ ] App Together (Flutter/React Native) — connect to mobile APIs
- [ ] Hotel Management adapter — live connection
- [ ] Tour Booking adapter — live connection
- [ ] Marketplace adapter — live connection

### Q3 — Third-Party Live Connections
- [ ] Payment Gateway live (Stripe + PromptPay)
- [ ] SMS Service live (Twilio / local provider)
- [ ] AI Call Center integration
- [ ] Cloud Point system live
- [ ] HelpDesk system live
- [ ] Push Notifications (Firebase FCM)
- [ ] Media CDN (Cloudinary / S3 for merchant images)

### Q4 — Scale & Optimize
- [ ] Load testing & performance optimization
- [ ] Monitoring (Telescope + Grafana)
- [ ] CI/CD pipeline hardening
- [ ] Security audit & penetration testing
- [ ] Data analytics pipeline
- [ ] Prepare for Phase 2 multi-cluster expansion

---

## Third-Party Service Expansion Plan

### Current Integrations (9 categories)
| # | Category | Adapter | Status |
|---|----------|---------|--------|
| 1 | Payment Gateway | PaymentAdapter | ✅ Built |
| 2 | SMS Service | SMSAdapter | ✅ Built |
| 3 | AI Call Center | AICallCenterAdapter | ✅ Built |
| 4 | Cloud Point | CloudPointAdapter | ✅ Built |
| 5 | Data Exchange | DataExchangeAdapter | ✅ Built |
| 6 | Authorization | AuthorizationAdapter | ✅ Built |
| 7 | HelpDesk | HelpDeskAdapter | ✅ Built |
| 8 | Translate | TranslateAdapter | ✅ Built |
| 9 | TTS | TTSAdapter | ✅ Built |

### Planned Additional Services
| # | Category | Provider Options | Priority | Purpose |
|---|----------|-----------------|----------|---------|
| 10 | Maps & Geo | Google Maps / Mapbox | P0 | Nearby search, directions, ETA for tourist navigation |
| 11 | Push Notification | Firebase FCM / APNs | P0 | Real-time alerts for bookings, deals, emergencies |
| 12 | Media Storage | Cloudinary / AWS S3 | P0 | Merchant photos, journey images, user uploads |
| 13 | Analytics | Mixpanel / GA4 | P1 | Tourist behavior tracking, funnel analysis |
| 14 | Currency Exchange | ExchangeRate-API / Fixer | P1 | Real-time THB/USD/EUR/CNY conversion for tourists |
| 15 | Weather | OpenWeatherMap | P1 | Destination weather for journey planning |
| 16 | Review Sync | TripAdvisor / Google Reviews | P2 | Sync external reviews to merchant profiles |
| 17 | Identity (KYC) | Jumio / Sumsub | P2 | Merchant identity verification |
| 18 | Email | SendGrid / Mailgun | P1 | Booking confirmations, marketing campaigns |
| 19 | Calendar | Google Calendar API | P2 | Sync bookings to tourist's calendar |
| 20 | Insurance | Travel insurance APIs | P3 | Offer travel insurance at booking |

### Service Integration Architecture
```
┌─────────────────────────────────────────────────────────────────┐
│                    API Gateway (Central)                         │
│              Rate Limiting / Auth / Circuit Breaker              │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Tier 1 (P0 — Launch Critical)                                  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────────────┐   │
│  │ Payment  │ │   SMS    │ │  Maps &  │ │ Push Notification│   │
│  │ Gateway  │ │ Service  │ │   Geo    │ │  (FCM / APNs)    │   │
│  └──────────┘ └──────────┘ └──────────┘ └──────────────────┘   │
│  ┌──────────┐ ┌──────────┐                                      │
│  │  Media   │ │  AI/NLP  │                                      │
│  │ Storage  │ │ Services │                                      │
│  └──────────┘ └──────────┘                                      │
│                                                                  │
│  Tier 2 (P1 — Post-Launch)                                      │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │
│  │Analytics │ │ Currency │ │ Weather  │ │  Email   │          │
│  │ Tracking │ │ Exchange │ │  Data    │ │ Service  │          │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │
│                                                                  │
│  Tier 3 (P2/P3 — Growth Phase)                                  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │
│  │ Review   │ │   KYC    │ │ Calendar │ │Insurance │          │
│  │  Sync    │ │Identity  │ │  Sync    │ │  APIs    │          │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │
└─────────────────────────────────────────────────────────────────┘
```

---

## Load Testing Strategy

### Architecture Overview
```
┌─────────────────────────────────────────────────────────────────┐
│                   Load Test Infrastructure                       │
│                                                                  │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                    Test Orchestrator                       │  │
│  │              (k6 / Artillery / Locust)                    │  │
│  │                                                           │  │
│  │  ┌─────────────┐  ┌──────────────┐  ┌────────────────┐  │  │
│  │  │  Scenario   │  │   Scenario   │  │   Scenario     │  │  │
│  │  │  Generator  │  │   Runner     │  │   Reporter     │  │  │
│  │  └─────────────┘  └──────────────┘  └────────────────┘  │  │
│  └───────────────────────────────────────────────────────────┘  │
│                              │                                   │
│                              ▼                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                Thailand Together Platform                 │  │
│  │                                                           │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐               │  │
│  │  │  API     │  │  Web     │  │  WebSocket│               │  │
│  │  │  Layer   │  │  Layer   │  │  Layer    │               │  │
│  │  └────┬─────┘  └────┬─────┘  └─────┬────┘               │  │
│  │       └──────────────┼──────────────┘                     │  │
│  │                      ▼                                    │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐               │  │
│  │  │  MySQL   │  │  Redis   │  │  Queue   │               │  │
│  │  │  Pool    │  │  Cache   │  │  Workers │               │  │
│  │  └──────────┘  └──────────┘  └──────────┘               │  │
│  └───────────────────────────────────────────────────────────┘  │
│                              │                                   │
│                              ▼                                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │                   Monitoring Stack                        │  │
│  │                                                           │  │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐               │  │
│  │  │ Grafana  │  │Prometheus│  │ Telescope│               │  │
│  │  │Dashboard │  │ Metrics  │  │  Debug   │               │  │
│  │  └──────────┘  └──────────┘  └──────────┘               │  │
│  └───────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

### Test Scenarios

#### 1. Tourist Journey Simulation (Primary)
```
Scenario: "Peak Season Tourist Day"
Duration: 30 minutes ramp-up → 60 minutes sustained → 15 minutes ramp-down

Virtual Users: 1,000 → 5,000 → 10,000 concurrent

User Flow:
  1. Login via SSO (Google/Facebook)           — 2s think time
  2. GET /api/mobile/discover/home             — 3s think time
  3. GET /api/mobile/discover/journeys         — 5s think time
  4. GET /api/mobile/discover/journeys/{code}  — 3s think time
  5. GET /api/mobile/discover/merchants/nearby — 2s think time
  6. GET /api/mobile/discover/merchants/{id}   — 5s think time
  7. POST /api/mobile/bookings                 — 10s think time
  8. GET /api/mobile/deals                     — 3s think time
  9. POST /api/mobile/deals/{id}/redeem        — 5s think time
  10. GET /api/mobile/discover/recommendations — 3s think time
```

#### 2. Merchant Operations (Secondary)
```
Scenario: "Merchant Daily Operations"
Virtual Users: 100 → 500 concurrent

User Flow:
  1. Login (email/password)
  2. GET /api/merchants/search (dashboard)
  3. POST /merchant/checkin (process tourist check-in)
  4. GET /merchant/{id}/reviews (check reviews)
  5. CRUD operations on bookings
```

#### 3. Admin Panel (Tertiary)
```
Scenario: "Admin Panel Usage"
Virtual Users: 10 → 50 concurrent

User Flow:
  1. Login admin
  2. Browse clusters, users, roles
  3. CRUD operations
  4. View analytics/reports
```

#### 4. API Integration Stress Test
```
Scenario: "Third-Party API Resilience"
Virtual Users: 500 concurrent

Test:
  - Payment creation under load
  - SMS sending burst (100/sec)
  - AI translation concurrent requests
  - Cloud Point earn/redeem rapid fire
  - Circuit breaker activation test
  - Graceful degradation when 3rd party is down
```

#### 5. Database Stress Test
```
Scenario: "Database Bottleneck Detection"

Tests:
  - Concurrent booking creation (deadlock detection)
  - Heavy read on merchant views (vw_merchant_search_public)
  - Journey one-call endpoint with 1000 concurrent reads
  - Reward point transfer with concurrent balance checks
  - Cross-cluster recommendation queries under load
```

### Performance Targets (SLA)

| Metric | Target | Critical |
|--------|--------|----------|
| API Response Time (p50) | < 200ms | < 500ms |
| API Response Time (p95) | < 500ms | < 1500ms |
| API Response Time (p99) | < 1000ms | < 3000ms |
| Error Rate | < 0.1% | < 1% |
| Throughput | > 500 req/s | > 200 req/s |
| Concurrent Users | 5,000 | 1,000 |
| Database Query Time (p95) | < 100ms | < 500ms |
| Cache Hit Rate | > 90% | > 75% |
| Uptime | 99.9% | 99.5% |

### Load Test Tooling

| Tool | Purpose | Why |
|------|---------|-----|
| **k6** (primary) | API load testing | JavaScript-based, CI/CD friendly, excellent metrics |
| **Artillery** (secondary) | Scenario-based testing | YAML config, good for complex user flows |
| **Laravel Telescope** | Request profiling | Built-in, zero config, query analysis |
| **Prometheus + Grafana** | Real-time monitoring | Industry standard, alerting built-in |
| **MySQL slow query log** | DB bottleneck detection | Native MySQL profiling |
| **Redis Monitor** | Cache performance | Track cache hit/miss rates |

### Load Test Directory Structure
```
loadtests/
├── k6/
│   ├── scenarios/
│   │   ├── tourist-journey.js        # Primary user flow
│   │   ├── merchant-operations.js    # Merchant flow
│   │   ├── admin-panel.js            # Admin flow
│   │   ├── api-integration-stress.js # Third-party resilience
│   │   └── database-stress.js        # DB bottleneck detection
│   ├── helpers/
│   │   ├── auth.js                   # SSO/login helpers
│   │   ├── data-generators.js        # Random test data
│   │   └── assertions.js             # Custom checks
│   ├── config/
│   │   ├── thresholds.json           # SLA thresholds
│   │   └── environments.json         # staging/production configs
│   └── run.sh                        # Quick-start script
├── artillery/
│   ├── tourist-flow.yml
│   └── merchant-flow.yml
├── monitoring/
│   ├── docker-compose.monitoring.yml # Prometheus + Grafana
│   ├── grafana/
│   │   └── dashboards/
│   │       ├── api-performance.json
│   │       └── database-health.json
│   └── prometheus/
│       └── prometheus.yml
├── reports/                          # Generated reports
│   └── .gitkeep
└── README.md
```

### CI/CD Integration
```yaml
# .github/workflows/load-test.yml
name: Load Test (Staging)
on:
  schedule:
    - cron: '0 2 * * 1'  # Every Monday 2 AM
  workflow_dispatch:
    inputs:
      scenario:
        description: 'Test scenario'
        required: true
        default: 'tourist-journey'
      vus:
        description: 'Virtual users'
        required: true
        default: '100'

jobs:
  load-test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: grafana/k6-action@v0.3.1
        with:
          filename: loadtests/k6/scenarios/${{ inputs.scenario }}.js
          flags: --vus ${{ inputs.vus }} --duration 5m
      - uses: actions/upload-artifact@v4
        with:
          name: load-test-report
          path: loadtests/reports/
```

---

## Phase 2: Multi-Cluster Expansion (Year 2+)

### Q1-Q2 — Second Cluster
- [ ] Chiang Mai cluster setup
- [ ] Cross-cluster reward exchange (Pattaya ↔ Chiang Mai)
- [ ] Tourist exchange recommendations
- [ ] Country-level campaigns (Thailand-wide)
- [ ] Shared analytics dashboard

### Q3-Q4 — International Expansion
- [ ] Vietnam country setup
- [ ] Danang cluster
- [ ] Multi-currency support
- [ ] Multi-language content management
- [ ] Cross-country reward exchange
- [ ] Global campaign system

### Technical Scaling
- [ ] Kubernetes cluster auto-scaling
- [ ] Database read replicas per cluster
- [ ] CDN optimization per region
- [ ] API Gateway rate limiting per cluster
- [ ] Distributed caching strategy
- [ ] Event-driven architecture (RabbitMQ/Kafka)

---

## Risk Register

| Risk | Impact | Mitigation |
|------|--------|------------|
| Peak season traffic spike | High | Auto-scaling + CDN + caching strategy |
| Third-party API downtime | Medium | Circuit breaker pattern + fallback responses |
| Data privacy (PDPA/GDPR) | High | Data residency per country, consent management |
| Payment fraud | High | Transaction monitoring, velocity checks |
| Multi-tenant data leak | Critical | Row-level security, cluster isolation testing |
| Database performance | Medium | Read replicas, query optimization, indexing |
