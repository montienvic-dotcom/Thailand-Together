@extends('layouts.admin')

@section('title', 'Development Roadmap')

@section('content')
    <div class="space-y-8">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Development Roadmap & Checklist</h1>
            <p class="mt-1 text-sm text-gray-500">แผนพัฒนาและ Checklist สิ่งที่ต้องทำ — Thailand Together Platform Phase 1 (Pattaya)</p>
        </div>

        {{-- Progress Summary --}}
        @php
            $phases = [
                ['name' => 'Foundation', 'done' => 8, 'total' => 8],
                ['name' => 'Core Features', 'done' => 0, 'total' => 12],
                ['name' => 'Integrations', 'done' => 0, 'total' => 10],
                ['name' => 'Production', 'done' => 0, 'total' => 8],
            ];
            $totalDone = collect($phases)->sum('done');
            $totalAll = collect($phases)->sum('total');
            $pct = $totalAll > 0 ? round(($totalDone / $totalAll) * 100) : 0;
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-bold text-gray-900">Overall Progress / ความคืบหน้ารวม</h2>
                <span class="text-2xl font-bold text-(--color-primary)">{{ $pct }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 mb-4">
                <div class="bg-(--color-primary) h-3 rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($phases as $phase)
                    @php $phasePct = $phase['total'] > 0 ? round(($phase['done'] / $phase['total']) * 100) : 0; @endphp
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-xs text-gray-500">{{ $phase['name'] }}</p>
                        <p class="text-lg font-bold text-gray-900">{{ $phase['done'] }}/{{ $phase['total'] }}</p>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                            <div class="bg-(--color-primary) h-1.5 rounded-full" style="width: {{ $phasePct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- PHASE 1: FOUNDATION (DONE) --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-green-50 border-b border-green-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500 text-white font-bold text-sm">1</span>
                        <div>
                            <h2 class="font-bold text-green-900">Phase 1: Platform Foundation / โครงสร้างพื้นฐาน</h2>
                            <p class="text-xs text-green-700">สร้างเสร็จแล้ว — ระบบหลักพร้อมใช้งาน</p>
                        </div>
                    </div>
                    <x-ui.badge color="green">8/8 Complete</x-ui.badge>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @php
                        $foundation = [
                            ['title' => 'Laravel 11+ Multi-Tenant Architecture', 'desc' => 'โครงสร้าง Laravel พร้อม Cluster-aware middleware, Country-aware middleware, multi-database support'],
                            ['title' => 'SSO & Authentication System', 'desc' => 'Login ด้วย Email/Password, SSO providers (Google, Facebook, LINE, Apple), Sanctum token, session auth'],
                            ['title' => 'Permission System (7-Step Resolution)', 'desc' => 'Role hierarchy (Global/Country/Cluster/App Admin), Group-based access, User-specific overrides, Module-level control'],
                            ['title' => 'Global Header Menu (Dynamic)', 'desc' => 'Header ที่กรองตามสิทธิ์อัตโนมัติ, Cluster Switcher, User Menu, Mobile responsive'],
                            ['title' => 'Admin Panel (Dashboard, Applications, Permissions)', 'desc' => 'Backend admin: Dashboard stats, Application CRUD, Permission hub (Users/Groups/Roles), API Reference'],
                            ['title' => 'API Integration Layer', 'desc' => 'RESTful API: Auth, Clusters, Menu, Admin APIs — พร้อม Sandbox testing, bilingual docs (TH/EN)'],
                            ['title' => 'Pattaya Cluster Setup (10 Apps, 62 Modules)', 'desc' => 'ข้อมูล seeded: 10 applications, 62 modules, 7 roles, 4 groups, demo users, API providers'],
                            ['title' => 'UI Component Library', 'desc' => 'Blade components: Card, Table, Badge, Button, Modal, Dropdown, Alert, Stat Card, Empty State, Icon system'],
                        ];
                    @endphp
                    @foreach($foundation as $item)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-green-50/50">
                            <div class="flex-shrink-0 w-5 h-5 rounded bg-green-500 flex items-center justify-center mt-0.5">
                                <x-icon name="check" class="w-3 h-3 text-white" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item['title'] }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- PHASE 2: CORE FEATURES --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-orange-50 border-b border-orange-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-500 text-white font-bold text-sm">2</span>
                        <div>
                            <h2 class="font-bold text-orange-900">Phase 2: Core Features / ฟีเจอร์หลัก</h2>
                            <p class="text-xs text-orange-700">กำลังดำเนินการ — ฟีเจอร์ที่ต้องสร้างต่อจาก Foundation</p>
                        </div>
                    </div>
                    <x-ui.badge color="orange">0/12 Pending</x-ui.badge>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @php
                        $coreFeatures = [
                            [
                                'title' => 'CRUD Operations สำหรับ Admin Panel',
                                'priority' => 'Critical',
                                'desc' => 'Create/Update/Delete สำหรับ Applications, Modules, Users, Groups, Roles — ตอนนี้มีแค่ Read',
                                'tasks' => ['Create Application form + validation', 'Edit Application (name, code, type, status)', 'Delete Application (soft delete)', 'Create/Edit Module within Application', 'Create/Edit User + assign to group/role', 'Create/Edit Group + mass assign users', 'Bulk operations (enable/disable/delete)'],
                            ],
                            [
                                'title' => 'Search & Filter ทุกหน้า',
                                'priority' => 'High',
                                'desc' => 'เพิ่ม search bar, column sorting, pagination, filters ให้ทุกตาราง',
                                'tasks' => ['Global search component (Livewire)', 'Per-column sorting', 'Advanced filters (status, type, date)', 'Paginated tables with configurable page size'],
                            ],
                            [
                                'title' => 'Permission Management UI (Interactive)',
                                'priority' => 'Critical',
                                'desc' => 'หน้า drag-drop/checkbox สำหรับ assign App/Module access ให้ User/Group',
                                'tasks' => ['Checkbox matrix: Apps x Modules for User', 'Same matrix for Groups', 'Visual role assignment with scope selector', 'Bulk permission import/export (CSV/JSON)', 'Permission audit log viewer'],
                            ],
                            [
                                'title' => 'Notification System',
                                'priority' => 'High',
                                'desc' => 'ระบบ notification real-time — bell icon, toast, email digest',
                                'tasks' => ['Database notification model (Laravel Notifications)', 'Real-time via WebSocket (Pusher/Soketi)', 'Bell icon with unread count in header', 'Toast notifications for actions', 'Email notification digest (daily/weekly)', 'Notification preferences per user'],
                            ],
                            [
                                'title' => 'File Upload & Media Management',
                                'priority' => 'Medium',
                                'desc' => 'อัพโหลดรูป, PDF, เอกสาร — image optimization, storage (S3/local)',
                                'tasks' => ['File upload component (drag & drop)', 'Image optimization (resize, compress, WebP)', 'S3-compatible storage driver', 'Media library per cluster', 'Avatar upload for users/apps'],
                            ],
                            [
                                'title' => 'Audit Log & Activity Tracking',
                                'priority' => 'High',
                                'desc' => 'บันทึกทุก action ที่เกิดขึ้นในระบบ — who did what when',
                                'tasks' => ['Activity log model (Spatie Activity Log)', 'Log all CRUD operations', 'Log permission changes', 'Log login/logout events', 'Admin viewer with filters', 'Export to CSV/PDF'],
                            ],
                            [
                                'title' => 'Dashboard Analytics (Interactive Charts)',
                                'priority' => 'Medium',
                                'desc' => 'กราฟแสดงข้อมูลเชิงลึก — user growth, bookings, revenue',
                                'tasks' => ['Chart.js or ApexCharts integration', 'User registration trend (line chart)', 'Application usage (bar chart)', 'Cluster comparison (radar chart)', 'Date range selector', 'Export charts as PNG/PDF'],
                            ],
                            [
                                'title' => 'Multi-Language UI (i18n)',
                                'priority' => 'Medium',
                                'desc' => 'เปลี่ยนภาษา UI ได้ — TH, EN, ZH, JA, KO',
                                'tasks' => ['Laravel lang files for TH/EN', 'Language switcher component', 'Auto-detect browser language', 'Translation management in Admin', 'RTL support preparation'],
                            ],
                            [
                                'title' => 'Email Verification & 2FA',
                                'priority' => 'High',
                                'desc' => 'ยืนยันอีเมลหลังสมัคร + Two-Factor Authentication',
                                'tasks' => ['Email verification flow (Laravel built-in)', 'TOTP-based 2FA (Google Authenticator)', 'SMS OTP as alternative 2FA', 'Recovery codes', 'Admin can enforce 2FA per role'],
                            ],
                            [
                                'title' => 'User Registration (Self-service)',
                                'priority' => 'High',
                                'desc' => 'หน้าสมัครสมาชิก — tourists สมัครเอง, merchants ต้อง approve',
                                'tasks' => ['Tourist registration form (quick)', 'Merchant registration with business info', 'Admin approval workflow for merchants', 'Welcome email with getting started guide', 'Profile completion wizard'],
                            ],
                            [
                                'title' => 'User Profile Management',
                                'priority' => 'Medium',
                                'desc' => 'หน้าแก้ไข profile — ชื่อ, รูป, password, preferences',
                                'tasks' => ['Profile edit page', 'Avatar upload', 'Password change', 'Notification preferences', 'Language preference', 'Connected SSO accounts management'],
                            ],
                            [
                                'title' => 'Data Export & Reporting',
                                'priority' => 'Medium',
                                'desc' => 'Export ข้อมูลเป็น CSV, Excel, PDF',
                                'tasks' => ['Export users list (CSV/Excel)', 'Export applications & modules', 'Export permission matrix', 'Export audit logs', 'Scheduled report generation', 'Email reports to admins'],
                            ],
                        ];
                    @endphp
                    @foreach($coreFeatures as $item)
                        <div class="border border-gray-200 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-50 transition-colors">
                                <div class="flex-shrink-0 w-5 h-5 rounded border-2 border-gray-300 mt-0.5"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $item['title'] }}</p>
                                        <span @class([
                                            'text-xs px-1.5 py-0.5 rounded font-medium',
                                            'bg-red-100 text-red-700' => $item['priority'] === 'Critical',
                                            'bg-orange-100 text-orange-700' => $item['priority'] === 'High',
                                            'bg-blue-100 text-blue-700' => $item['priority'] === 'Medium',
                                        ])>{{ $item['priority'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                                </div>
                                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 flex-shrink-0" ::class="open ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open" x-collapse x-cloak class="border-t border-gray-100 px-3 py-3 bg-gray-50">
                                <p class="text-xs font-semibold text-gray-500 mb-2">Sub-tasks:</p>
                                <div class="space-y-1.5">
                                    @foreach($item['tasks'] as $task)
                                        <div class="flex items-center gap-2">
                                            <div class="w-3.5 h-3.5 rounded border border-gray-300 flex-shrink-0"></div>
                                            <span class="text-xs text-gray-700">{{ $task }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- PHASE 3: THIRD-PARTY INTEGRATIONS --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-blue-50 border-b border-blue-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white font-bold text-sm">3</span>
                        <div>
                            <h2 class="font-bold text-blue-900">Phase 3: Third-Party Integrations / เชื่อมต่อระบบภายนอก</h2>
                            <p class="text-xs text-blue-700">เชื่อมต่อ API ภายนอก — Payment, SMS, AI, Cloud Points</p>
                        </div>
                    </div>
                    <x-ui.badge color="blue">0/10 Pending</x-ui.badge>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @php
                        $integrations = [
                            [
                                'title' => 'Payment Gateway Integration',
                                'priority' => 'Critical',
                                'desc' => 'เชื่อมต่อระบบชำระเงิน — Stripe, PromptPay (QR), Local gateways',
                                'tasks' => ['Stripe SDK integration', 'PromptPay QR code generation', 'Payment callback/webhook handler', 'Refund management', 'Transaction history & reconciliation', 'Multi-currency support (THB, USD, VND)'],
                            ],
                            [
                                'title' => 'SMS Service Integration',
                                'priority' => 'High',
                                'desc' => 'ส่ง SMS — OTP, notifications, marketing',
                                'tasks' => ['Twilio SDK integration', 'Local Thai SMS provider (ThaiBulkSMS)', 'OTP flow: send, verify, expire', 'SMS template management', 'Delivery tracking & reporting', 'Rate limiting per user'],
                            ],
                            [
                                'title' => 'AI Chatbot Integration',
                                'priority' => 'High',
                                'desc' => 'Chatbot อัจฉริยะ — ตอบคำถาม แนะนำ จอง',
                                'tasks' => ['AI Agent API connection', 'Context-aware conversation (cluster/app)', 'Multi-language support', 'Handoff to human agent', 'Conversation history storage', 'Chat widget component (floating)'],
                            ],
                            [
                                'title' => 'Translation API Integration',
                                'priority' => 'Medium',
                                'desc' => 'แปลภาษาอัตโนมัติ — TH, EN, ZH, JA, KO, RU',
                                'tasks' => ['Translation API adapter', 'Auto-translate user content', 'Cache translated content', 'Detect source language', 'Admin review & correction UI'],
                            ],
                            [
                                'title' => 'Text-to-Speech (TTS) Integration',
                                'priority' => 'Low',
                                'desc' => 'อ่านออกเสียง — navigation, announcements, accessibility',
                                'tasks' => ['TTS API adapter', 'Audio player component', 'Cache generated audio files', 'Multiple voice options', 'Speed/pitch control'],
                            ],
                            [
                                'title' => 'Cloud Point / Reward System',
                                'priority' => 'Critical',
                                'desc' => 'ระบบสะสมแต้ม — Earn, Redeem, Transfer, Exchange ข้าม Cluster',
                                'tasks' => ['Point balance model & API', 'Earn rules engine (per action)', 'Redemption catalog', 'Transfer between users', 'Cross-cluster exchange rate system', 'Point expiry management', 'Transaction ledger (double-entry)', 'Points dashboard for users'],
                            ],
                            [
                                'title' => 'AI Call Center Integration',
                                'priority' => 'Medium',
                                'desc' => 'Call center อัตโนมัติ — IVR, routing, recording',
                                'tasks' => ['AI Call Center API connection', 'IVR menu configuration', 'Call routing by cluster/language', 'Call recording & transcription', 'Agent dashboard', 'Escalation to human'],
                            ],
                            [
                                'title' => 'Data Exchange API',
                                'priority' => 'Medium',
                                'desc' => 'Import/Export/Sync ข้อมูลระหว่างระบบ',
                                'tasks' => ['Data import adapter (CSV, JSON, XML)', 'Data export scheduler', 'Webhook system for real-time sync', 'Data transformation pipeline', 'Conflict resolution strategy', 'Audit trail for all exchanges'],
                            ],
                            [
                                'title' => 'HelpDesk API Integration',
                                'priority' => 'High',
                                'desc' => 'เชื่อมต่อระบบ Ticket Support — สร้าง ติดตาม ปิด Ticket',
                                'tasks' => ['Ticket creation API', 'Status tracking & updates', 'SLA timer integration', 'Escalation workflow', 'Customer satisfaction survey', 'Knowledge base search'],
                            ],
                            [
                                'title' => 'External App Adapter System',
                                'priority' => 'High',
                                'desc' => 'ระบบเชื่อมต่อ App ภายนอกเข้ากับ Platform (iframe/micro-frontend)',
                                'tasks' => ['Adapter interface definition', 'iframe wrapper with SSO pass-through', 'Micro-frontend loader (Module Federation)', 'External app registration in Admin', 'API gateway proxy for external apps', 'Patch management system implementation'],
                            ],
                        ];
                    @endphp
                    @foreach($integrations as $item)
                        <div class="border border-gray-200 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-50 transition-colors">
                                <div class="flex-shrink-0 w-5 h-5 rounded border-2 border-gray-300 mt-0.5"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $item['title'] }}</p>
                                        <span @class([
                                            'text-xs px-1.5 py-0.5 rounded font-medium',
                                            'bg-red-100 text-red-700' => $item['priority'] === 'Critical',
                                            'bg-orange-100 text-orange-700' => $item['priority'] === 'High',
                                            'bg-blue-100 text-blue-700' => $item['priority'] === 'Medium',
                                            'bg-gray-100 text-gray-600' => $item['priority'] === 'Low',
                                        ])>{{ $item['priority'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                                </div>
                                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 flex-shrink-0" ::class="open ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open" x-collapse x-cloak class="border-t border-gray-100 px-3 py-3 bg-gray-50">
                                <p class="text-xs font-semibold text-gray-500 mb-2">Sub-tasks:</p>
                                <div class="space-y-1.5">
                                    @foreach($item['tasks'] as $task)
                                        <div class="flex items-center gap-2">
                                            <div class="w-3.5 h-3.5 rounded border border-gray-300 flex-shrink-0"></div>
                                            <span class="text-xs text-gray-700">{{ $task }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- PHASE 4: PRODUCTION READINESS --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-purple-50 border-b border-purple-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-500 text-white font-bold text-sm">4</span>
                        <div>
                            <h2 class="font-bold text-purple-900">Phase 4: Production Readiness / พร้อม Production</h2>
                            <p class="text-xs text-purple-700">DevOps, Testing, Security, Performance — ก่อน Go-Live</p>
                        </div>
                    </div>
                    <x-ui.badge color="purple">0/8 Pending</x-ui.badge>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @php
                        $production = [
                            [
                                'title' => 'Docker & Kubernetes Setup',
                                'priority' => 'Critical',
                                'desc' => 'Container deployment — Docker Compose (dev), K8s (production), per-cluster namespace',
                                'tasks' => ['Dockerfile for Laravel app', 'Docker Compose for local development', 'K8s deployment manifests', 'Per-cluster namespace isolation', 'Ingress configuration with SSL', 'Auto-scaling rules (HPA)', 'Health checks & readiness probes'],
                            ],
                            [
                                'title' => 'CI/CD Pipeline (GitHub Actions)',
                                'priority' => 'Critical',
                                'desc' => 'Automated testing, build, deploy — PR checks, staging, production',
                                'tasks' => ['PHPUnit tests on PR', 'Lint & code style checks (Pint)', 'Build Docker image on merge', 'Deploy to staging automatically', 'Manual approval for production', 'Database migration automation', 'Rollback strategy'],
                            ],
                            [
                                'title' => 'Testing Suite',
                                'priority' => 'Critical',
                                'desc' => 'Unit tests, Feature tests, Integration tests — minimum 80% coverage',
                                'tasks' => ['Unit tests for Services (SSO, Permission, Cluster)', 'Feature tests for all API endpoints', 'Feature tests for Admin CRUD', 'Browser tests (Dusk) for critical flows', 'Factory & Seeder for test data', 'Test database configuration', 'Coverage report generation'],
                            ],
                            [
                                'title' => 'Security Hardening',
                                'priority' => 'Critical',
                                'desc' => 'OWASP Top 10, rate limiting, CORS, CSP, encryption',
                                'tasks' => ['Rate limiting per endpoint (Throttle)', 'CORS configuration per cluster', 'Content Security Policy headers', 'SQL injection protection audit', 'XSS protection audit', 'CSRF protection verification', 'Password hashing (bcrypt/argon2)', 'Sensitive data encryption at rest', 'Security headers (HSTS, X-Frame-Options)'],
                            ],
                            [
                                'title' => 'Performance Optimization',
                                'priority' => 'High',
                                'desc' => 'Caching, query optimization, CDN, lazy loading',
                                'tasks' => ['Redis caching for API responses', 'Database query optimization (N+1)', 'Eager loading audit', 'CDN for static assets', 'Image lazy loading', 'API response compression (gzip)', 'Database indexing review', 'Laravel Octane evaluation'],
                            ],
                            [
                                'title' => 'Monitoring & Observability',
                                'priority' => 'High',
                                'desc' => 'Application monitoring, error tracking, alerting',
                                'tasks' => ['Laravel Telescope (development)', 'Sentry/Bugsnag for error tracking', 'Grafana + Prometheus for metrics', 'Log aggregation (ELK stack)', 'Health check endpoints', 'Uptime monitoring', 'Alert rules (Slack/email)'],
                            ],
                            [
                                'title' => 'Backup & Disaster Recovery',
                                'priority' => 'High',
                                'desc' => 'Automated backups, recovery plan, data retention',
                                'tasks' => ['Automated database backups (daily)', 'File storage backups', 'Cross-region backup replication', 'Recovery testing schedule', 'Data retention policy', 'Point-in-time recovery capability'],
                            ],
                            [
                                'title' => 'Documentation & Training',
                                'priority' => 'Medium',
                                'desc' => 'Technical docs, API docs, user manual, training materials',
                                'tasks' => ['API documentation (OpenAPI/Swagger)', 'Developer onboarding guide', 'System architecture diagrams', 'Admin user manual', 'Merchant onboarding guide', 'Video tutorials (key workflows)', 'Runbook for ops team'],
                            ],
                        ];
                    @endphp
                    @foreach($production as $item)
                        <div class="border border-gray-200 rounded-lg overflow-hidden" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full flex items-center gap-3 p-3 text-left hover:bg-gray-50 transition-colors">
                                <div class="flex-shrink-0 w-5 h-5 rounded border-2 border-gray-300 mt-0.5"></div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $item['title'] }}</p>
                                        <span @class([
                                            'text-xs px-1.5 py-0.5 rounded font-medium',
                                            'bg-red-100 text-red-700' => $item['priority'] === 'Critical',
                                            'bg-orange-100 text-orange-700' => $item['priority'] === 'High',
                                            'bg-blue-100 text-blue-700' => $item['priority'] === 'Medium',
                                        ])>{{ $item['priority'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                                </div>
                                <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 flex-shrink-0" ::class="open ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open" x-collapse x-cloak class="border-t border-gray-100 px-3 py-3 bg-gray-50">
                                <p class="text-xs font-semibold text-gray-500 mb-2">Sub-tasks:</p>
                                <div class="space-y-1.5">
                                    @foreach($item['tasks'] as $task)
                                        <div class="flex items-center gap-2">
                                            <div class="w-3.5 h-3.5 rounded border border-gray-300 flex-shrink-0"></div>
                                            <span class="text-xs text-gray-700">{{ $task }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- RECOMMENDED PRIORITY ORDER --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="font-bold text-gray-900 mb-4">Recommended Priority Order / ลำดับที่แนะนำ</h2>
            <p class="text-sm text-gray-600 mb-4">ลำดับการพัฒนาที่แนะนำ เริ่มจากสิ่งที่สำคัญที่สุดก่อน:</p>

            <div class="space-y-3">
                @php
                    $priorities = [
                        ['rank' => 1, 'title' => 'CRUD Operations (Admin Panel)', 'reason' => 'Admin ต้องจัดการข้อมูลได้จริง — เป็นพื้นฐานของทุกอย่าง', 'color' => 'red'],
                        ['rank' => 2, 'title' => 'Permission Management UI', 'reason' => 'ต้องกำหนดสิทธิ์ได้จริงจากหน้าเว็บ — ไม่ใช่แค่ API', 'color' => 'red'],
                        ['rank' => 3, 'title' => 'User Registration + Email Verification', 'reason' => 'ให้ผู้ใช้สมัครได้ — Tourist, Merchant', 'color' => 'red'],
                        ['rank' => 4, 'title' => 'Payment Gateway (Stripe + PromptPay)', 'reason' => 'เปิดรับเงินได้ — ระบบจองต้องมีการชำระเงิน', 'color' => 'red'],
                        ['rank' => 5, 'title' => 'Cloud Point / Reward System', 'reason' => 'หัวใจของ Super App — สะสมแต้ม เพิ่ม engagement', 'color' => 'orange'],
                        ['rank' => 6, 'title' => 'Testing Suite (80%+ coverage)', 'reason' => 'ป้องกัน bug ก่อนเพิ่มฟีเจอร์ใหม่', 'color' => 'orange'],
                        ['rank' => 7, 'title' => 'Notification System', 'reason' => 'แจ้งเตือนผู้ใช้ — booking confirmation, promotions', 'color' => 'orange'],
                        ['rank' => 8, 'title' => 'SMS OTP + 2FA', 'reason' => 'ความปลอดภัย — จำเป็นสำหรับ payment flows', 'color' => 'orange'],
                        ['rank' => 9, 'title' => 'AI Chatbot Integration', 'reason' => 'ช่วยนักท่องเที่ยว 24/7 — ลดภาระ support', 'color' => 'blue'],
                        ['rank' => 10, 'title' => 'Docker + CI/CD + Monitoring', 'reason' => 'พร้อม deploy production — automated pipeline', 'color' => 'blue'],
                    ];
                @endphp
                @foreach($priorities as $p)
                    <div class="flex items-center gap-3 bg-{{ $p['color'] }}-50/50 rounded-lg p-3">
                        <span class="flex-shrink-0 w-7 h-7 rounded-full bg-{{ $p['color'] }}-500 text-white text-xs font-bold flex items-center justify-center">{{ $p['rank'] }}</span>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900">{{ $p['title'] }}</p>
                            <p class="text-xs text-gray-500">{{ $p['reason'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
