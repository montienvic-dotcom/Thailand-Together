# Thailand Together - Global Tourism Super App Platform

## Project Overview

Thailand Together เป็น **Global Tourism Platform** สำหรับให้บริการนักท่องเที่ยว
ออกแบบเป็น Multi-Country, Multi-Cluster architecture ที่สามารถขยายไปยังเมืองต่างๆ
ทั่วโลกได้ โดยเริ่มต้น Phase 1 ที่ **Pattaya, Thailand** เป็นเวลา 1 ปี

---

## Architecture Hierarchy

```
┌─────────────────────────────────────────────────────────────────┐
│                    GLOBAL (Thailand Together)                     │
│           Central Management / Infrastructure / APIs             │
├─────────────────────────┬───────────────────────────────────────┤
│    COUNTRY: Thailand    │       COUNTRY: Vietnam                │
│                         │                                       │
│  ┌───────────────────┐  │  ┌───────────────────┐               │
│  │ CLUSTER: Pattaya  │  │  │ CLUSTER: Danang   │               │
│  │ (Phase 1 - Y1)    │  │  │ (Future)          │               │
│  │                   │  │  │                   │               │
│  │ ┌──────────────┐  │  │  │ ┌──────────────┐  │               │
│  │ │ App Together │  │  │  │ │ App Together │  │               │
│  │ │ Hotel Mgmt   │  │  │  │ │ Hotel Mgmt   │  │               │
│  │ │ Tour Booking │  │  │  │ │ Tour Booking │  │               │
│  │ │ Marketplace  │  │  │  │ │ Marketplace  │  │               │
│  │ │ ...more apps │  │  │  │ │ ...more apps │  │               │
│  │ └──────────────┘  │  │  │ └──────────────┘  │               │
│  └───────────────────┘  │  └───────────────────┘               │
│                         │                                       │
│  ┌───────────────────┐  │  ┌───────────────────┐               │
│  │ CLUSTER: Chiang   │  │  │ CLUSTER: Ho Chi   │               │
│  │ Mai (Future)      │  │  │ Minh (Future)     │               │
│  └───────────────────┘  │  └───────────────────┘               │
└─────────────────────────┴───────────────────────────────────────┘
```

### Hierarchy Levels (5 Levels)

```
Level 0: GLOBAL    → Thailand Together Platform (Central)
Level 1: COUNTRY   → Thailand, Vietnam, ...
Level 2: CLUSTER   → Pattaya, Danang, Chiang Mai, ...
Level 3: APP       → App Together, Hotel Mgmt, Tour Booking, ...
Level 4: MODULE    → Specific features within each app
```

---

## Three Pillars Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                        Thailand Together Platform                       │
│                                                                         │
│  Pillar 1               Pillar 2                Pillar 3               │
│  ┌─────────────────┐   ┌─────────────────────┐  ┌───────────────────┐  │
│  │  App Together   │   │  Supporting Systems  │  │  Third-Party      │  │
│  │  (Mobile App)   │   │  (Laravel Based)     │  │  Integrations     │  │
│  │                 │   │                     │  │                   │  │
│  │ • Tourist App   │   │ • Admin Backend     │  │ • Payment Gateway │  │
│  │ • Super App     │   │ • Hotel Management  │  │ • SMS Service     │  │
│  │   (after P1)    │   │ • Tour Booking      │  │ • AI Call Center  │  │
│  │ • Cross-cluster │   │ • Marketplace       │  │ • Cloud Point API │  │
│  │   features      │   │ • CRM               │  │ • Data Exchange   │  │
│  │                 │   │ • HelpDesk          │  │ • Authorization   │  │
│  │                 │   │ • Some → Mobile App │  │ • AI Agent API    │  │
│  │                 │   │                     │  │ • Translate API   │  │
│  │                 │   │                     │  │ • TTS API         │  │
│  │                 │   │                     │  │ • HelpDesk API    │  │
│  │                 │   │                     │  │ • (growing...)    │  │
│  └─────────────────┘   └─────────────────────┘  └───────────────────┘  │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Service Model (XaaS)

```
┌─────────────────────────────────────────────────────────────┐
│                    Thailand Together XaaS                     │
│                                                               │
│  ┌─────────────┐  ┌─────────────┐  ┌──────────┐  ┌────────┐ │
│  │    SaaS     │  │    IaaS     │  │   CaaS   │  │  DaaS  │ │
│  │             │  │             │  │          │  │        │ │
│  │ App Together│  │ Servers     │  │ Docker   │  │ Central│ │
│  │ Admin Panel │  │ Storage     │  │ K8s      │  │ Data   │ │
│  │ Hotel Mgmt  │  │ Network     │  │ Service  │  │ Lake   │ │
│  │ Tour Book   │  │ CDN         │  │ Mesh     │  │ BI/    │ │
│  │ Marketplace │  │ DNS per     │  │ Auto-    │  │ Analyt │ │
│  │ CRM         │  │ cluster     │  │ scale    │  │ ML/AI  │ │
│  │ (per clstr) │  │             │  │          │  │ Report │ │
│  └─────────────┘  └─────────────┘  └──────────┘  └────────┘ │
│                                                               │
│  Each new Cluster gets:                                       │
│  • Own SaaS instance (isolated data)                         │
│  • Shared IaaS (cost efficient)                              │
│  • Own container namespace (CaaS)                            │
│  • Own data partition + shared analytics (DaaS)              │
└─────────────────────────────────────────────────────────────┘
```

---

## SSO & Permission System

### Permission Hierarchy

```
GLOBAL ADMIN (Thailand Together Central)
│   → Can access ALL countries, ALL clusters, ALL apps, ALL modules
│
├── COUNTRY ADMIN (e.g., Thailand Admin)
│   │   → Can access ALL clusters within Thailand
│   │
│   ├── CLUSTER ADMIN (e.g., Pattaya Admin)
│   │   │   → Can access ALL apps within Pattaya cluster
│   │   │
│   │   ├── APP ADMIN (e.g., Hotel Mgmt Admin)
│   │   │   │   → Can access ALL modules within Hotel Mgmt app
│   │   │   │
│   │   │   └── MODULE ACCESS (e.g., Booking Module)
│   │   │       → Specific feature access
│   │   │
│   │   └── GROUP-BASED ACCESS
│   │       │
│   │       ├── Group: #Operators
│   │       │   └── User A → App#1[Mod 1,2,4] + App#3[Mod 1,5]
│   │       │
│   │       ├── Group: #Merchants
│   │       │   └── User B → App#2[Mod 1,3] + App#4[Mod 2]
│   │       │
│   │       └── Group: #Tourists
│   │           └── User C → AppTogether[Mod 1,2,3,4,5]
│   │
│   └── CLUSTER: Chiang Mai (same structure)
│
└── COUNTRY: Vietnam (same structure)
```

### Permission Resolution

```
User tries to access Module X in App Y in Cluster Z in Country W

Step 1: Authenticate via SSO (single token across all)
Step 2: Check Country-level permission  → W allowed?
Step 3: Check Cluster-level permission  → Z allowed?
Step 4: Check App-level permission      → Y allowed?
Step 5: Check Module-level permission   → X allowed?
Step 6: Check Group overrides           → any group grants/denies?
Step 7: Check User-specific overrides   → any user-level grants/denies?

Result: ALLOW or DENY (most specific rule wins)
```

---

## Cross-Cluster & Cross-Country Features

```
┌──────────────┐                          ┌──────────────┐
│   Pattaya    │    Cross-Cluster Bus     │   Danang     │
│   Cluster    │◄────────────────────────►│   Cluster    │
│              │                          │              │
│ • Tourists   │  ┌────────────────────┐  │ • Tourists   │
│ • Merchants  │  │  Shared Services   │  │ • Merchants  │
│ • Rewards    │  │                    │  │ • Rewards    │
│              │  │ • Reward Points    │  │              │
└──────┬───────┘  │   Exchange         │  └──────┬───────┘
       │          │ • Cross-Cluster    │         │
       │          │   Recommendations  │         │
       │          │ • Tourist Exchange │         │
       │          │   Programs         │         │
       │          │ • Country-level    │         │
       │          │   Campaigns        │         │
       │          │ • Shared Payment   │         │
       │          │ • Shared AI/SMS    │         │
       │          └────────────────────┘         │
       │                                         │
       └────────── Global Data Lake ─────────────┘
```

### Cross Features:
- **Tourist Exchange**: Recommend Danang to Pattaya tourists and vice versa
- **Reward Points**: Earn in Pattaya, redeem in Danang (exchange rate system)
- **Country Campaigns**: Thailand-wide or Vietnam-wide promotions
- **Global Campaigns**: Cross-country benefit programs
- **Shared Analytics**: Tourist behavior across all clusters

---

## API Integration Layer

```
┌─────────────────────────────────────────────────────────────┐
│                   API Gateway (Central)                       │
│              Rate Limiting / Auth / Logging                   │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐   │
│  │   Payment    │  │     SMS      │  │   AI Services    │   │
│  │   Gateway    │  │   Service    │  │                  │   │
│  │              │  │              │  │ • AI Agent API   │   │
│  │ • Stripe     │  │ • Twilio     │  │ • AI Call Center │   │
│  │ • PromptPay  │  │ • Local SMS  │  │ • Translate API  │   │
│  │ • VNPay      │  │              │  │ • TTS API        │   │
│  │ • Local GW   │  │              │  │ • Chatbot        │   │
│  └──────────────┘  └──────────────┘  └──────────────────┘   │
│                                                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐   │
│  │  Cloud Point │  │    Data      │  │   HelpDesk       │   │
│  │     API      │  │  Exchange    │  │     API          │   │
│  │              │  │    API       │  │                  │   │
│  │ • Earn       │  │              │  │ • Ticket Mgmt   │   │
│  │ • Redeem     │  │ • Import     │  │ • SLA Tracking  │   │
│  │ • Transfer   │  │ • Export     │  │ • Escalation    │   │
│  │ • Exchange   │  │ • Sync       │  │                  │   │
│  └──────────────┘  └──────────────┘  └──────────────────┘   │
│                                                               │
│  ┌──────────────┐                                            │
│  │Authorization │  All APIs are:                             │
│  │    API       │  • Cluster-aware (know which cluster)      │
│  │              │  • Country-aware (know which country)       │
│  │ • OAuth2     │  • Rate-limited per cluster                │
│  │ • JWT        │  • Logged centrally                        │
│  │ • RBAC       │  • Shared across all clusters              │
│  └──────────────┘                                            │
└─────────────────────────────────────────────────────────────┘
```

---

## Patch Management System

CodeCanyon scripts may have bugs. We maintain patches separately:

```
patches/
├── registry.json              # Master list of all patches
├── {app-name}/
│   ├── patch-001.json         # Patch definition (find/replace)
│   ├── patch-002.json
│   └── overrides/             # Service Provider overrides
│       └── FixedController.php
└── engine/
    ├── PatchServiceProvider.php
    ├── PatchManager.php       # Apply, check, rollback
    └── Commands/
        ├── PatchApply.php     # php artisan patches:apply
        ├── PatchCheck.php     # php artisan patches:check
        └── PatchRollback.php  # php artisan patches:rollback
```

### Patch Workflow:
1. Claude scans CodeCanyon code → identifies bugs
2. Creates patch files (non-destructive, override-based)
3. After CodeCanyon update → run `php artisan patches:check`
4. Patches auto-detect conflicts and report status
5. Claude creates updated patches if needed

---

## Global Header Menu (Super App Shell)

```
┌─────────────────────────────────────────────────────────────┐
│  🌏 Thailand Together    [Pattaya ▼]    [TH ▼]    [👤 User]│
├─────────────────────────────────────────────────────────────┤
│  [App Together] [Hotels] [Tours] [Shop] [Rewards] [More ▼] │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│              ┌─────────────────────────┐                     │
│              │    Active App Content   │                     │
│              │    (iframe / micro-     │                     │
│              │     frontend / SPA)     │                     │
│              │                         │                     │
│              └─────────────────────────┘                     │
│                                                               │
├─────────────────────────────────────────────────────────────┤
│  [Notifications] [Chat Support] [Language] [Help]            │
└─────────────────────────────────────────────────────────────┘

Menu items are DYNAMIC based on:
• User's country & cluster
• User's group & permissions
• Assigned apps & modules
• Current context (tourist vs merchant vs admin)
```

---

## Database Architecture

### Multi-Tenant Strategy: Hybrid

```
Global DB (shared)          Cluster DB (per cluster)
├── countries               ├── cluster_users (local data)
├── clusters                ├── cluster_merchants
├── users (auth/SSO)        ├── cluster_bookings
├── roles                   ├── cluster_transactions
├── permissions             ├── cluster_rewards
├── apps                    ├── cluster_content
├── modules                 └── cluster_analytics
├── groups
├── api_integrations        App-specific DB (per app if needed)
├── global_campaigns        ├── app_specific_tables
├── reward_exchange_rates   └── ...
└── audit_logs
```

---

## Project Structure

```
Thailand-Together/
├── CLAUDE.md                          # This file
├── README.md
│
├── platform/                          # Laravel Main Application
│   ├── app/
│   │   ├── Models/
│   │   │   ├── Global/               # Global-level models
│   │   │   │   ├── Country.php
│   │   │   │   ├── Cluster.php
│   │   │   │   └── GlobalCampaign.php
│   │   │   ├── Auth/                 # SSO & Permission models
│   │   │   │   ├── User.php
│   │   │   │   ├── Role.php
│   │   │   │   ├── Permission.php
│   │   │   │   ├── Group.php
│   │   │   │   └── UserAppAccess.php
│   │   │   ├── App/                  # App registry models
│   │   │   │   ├── Application.php
│   │   │   │   └── Module.php
│   │   │   └── Integration/         # Third-party models
│   │   │       ├── ApiProvider.php
│   │   │       └── ApiCredential.php
│   │   │
│   │   ├── Services/
│   │   │   ├── SSO/                  # SSO Service
│   │   │   ├── Permission/          # Permission resolver
│   │   │   ├── Cluster/             # Cluster management
│   │   │   ├── CrossCluster/        # Cross-cluster features
│   │   │   ├── Reward/              # Reward point management
│   │   │   └── ApiGateway/          # API integration layer
│   │   │
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Admin/           # Backend admin controllers
│   │   │   │   ├── Api/             # API controllers
│   │   │   │   └── SuperApp/        # Super app shell controllers
│   │   │   ├── Middleware/
│   │   │   │   ├── ClusterAware.php
│   │   │   │   ├── CountryAware.php
│   │   │   │   └── CheckModuleAccess.php
│   │   │   └── Resources/           # API Resources
│   │   │
│   │   └── Providers/
│   │       ├── ClusterServiceProvider.php
│   │       └── ApiGatewayServiceProvider.php
│   │
│   ├── database/migrations/          # All migrations
│   ├── routes/
│   │   ├── api.php                   # API routes
│   │   ├── admin.php                 # Admin routes
│   │   └── superapp.php              # Super app routes
│   └── config/
│       ├── clusters.php              # Cluster configuration
│       └── integrations.php          # API integrations config
│
├── patches/                           # Patch Management System
│   ├── registry.json
│   └── engine/
│
├── adapters/                          # CodeCanyon app adapters
│   ├── adapter-interface.php
│   └── {app-name}/
│
└── docker/                            # Container configs per cluster
    ├── docker-compose.yml
    ├── global/
    └── cluster-template/
```

---

## Technology Stack

- **Backend**: Laravel 11+ (PHP 8.4)
- **Frontend**: Blade + Livewire (admin), Vue.js/React (Super App shell)
- **Mobile**: Flutter or React Native (App Together)
- **Database**: MySQL/PostgreSQL (per-cluster), Redis (cache/session)
- **Queue**: Redis / RabbitMQ
- **Search**: Meilisearch / Elasticsearch
- **Container**: Docker + Kubernetes
- **CI/CD**: GitHub Actions
- **Monitoring**: Laravel Telescope + Grafana

---

## Development Conventions

- Follow PSR-12 coding standard
- Use Laravel naming conventions (snake_case DB, camelCase PHP)
- All API responses use JSON:API or consistent envelope format
- All models must be cluster-aware (use ClusterScope trait)
- All permissions checked via PermissionService (never inline)
- Database migrations prefixed with cluster context
- Tests required for all SSO/Permission logic
- Environment config per cluster via .env.{cluster}

---

## Phase 1 Scope (Pattaya - Year 1)

1. ✅ Platform foundation (Laravel + multi-tenant)
2. ✅ SSO & Permission system
3. ✅ Global Header Menu
4. ✅ API Integration Layer
5. ✅ Patch Management System
6. ✅ Backend Admin System
7. ✅ Pattaya cluster setup
8. ⬜ App Together (Mobile) integration
9. ⬜ CodeCanyon apps integration
10. ⬜ Third-party API connections (live)
