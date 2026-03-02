<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add 5 new applications to the platform:
 * City Dashboard, Government ERP, Event & MICE, Project Management, Data Exchange
 *
 * These were added to the PlatformSeeder but the seeder skips on production
 * (because users already exist), so we need a migration to add them.
 */
return new class extends Migration
{
    public function up(): void
    {
        $pattayaId = DB::table('clusters')->where('slug', 'pattaya')->value('id');

        $apps = [
            [
                'name' => 'City Dashboard',
                'slug' => 'city-dashboard',
                'code' => 'CITY_DASHBOARD',
                'type' => 'web',
                'source' => 'internal',
                'icon' => 'presentation-chart-bar',
                'color' => '#0EA5E9',
                'sort_order' => 11,
                'description' => 'Real-time city intelligence — tourism statistics, traffic flow, revenue analytics, and public safety monitoring for city officials',
                'modules' => [
                    ['name' => 'Tourism Statistics', 'slug' => 'tourism-stats', 'code' => 'TOURISM_STATS', 'description' => 'Real-time visitor counts, nationality breakdown, spending analytics, seasonal trends', 'sort_order' => 1],
                    ['name' => 'Traffic & Mobility', 'slug' => 'traffic-mobility', 'code' => 'TRAFFIC_MOBILITY', 'description' => 'Live traffic flow, parking occupancy, public transport ridership, congestion heatmaps', 'sort_order' => 2],
                    ['name' => 'Revenue & Tax Monitor', 'slug' => 'revenue-tax', 'code' => 'REVENUE_TAX', 'description' => 'Hotel tax collection, tourism fee tracking, revenue forecasting per zone/category', 'sort_order' => 3],
                    ['name' => 'Public Safety Monitor', 'slug' => 'public-safety', 'code' => 'PUBLIC_SAFETY', 'description' => 'Incident tracking, crime heatmaps, emergency response times, CCTV integration', 'sort_order' => 4],
                    ['name' => 'Environmental Monitor', 'slug' => 'env-monitor', 'code' => 'ENV_MONITOR', 'description' => 'Air quality index, beach water quality, noise levels, waste management metrics', 'sort_order' => 5],
                    ['name' => 'Business Intelligence', 'slug' => 'city-bi', 'code' => 'CITY_BI', 'description' => 'Cross-data analytics, KPI scorecards, trend predictions, policy impact analysis', 'sort_order' => 6],
                    ['name' => 'Citizen Feedback', 'slug' => 'citizen-feedback', 'code' => 'CITIZEN_FEEDBACK', 'description' => 'Public complaints, satisfaction surveys, improvement suggestions, response tracking', 'sort_order' => 7],
                ],
            ],
            [
                'name' => 'Government ERP (NEW GFMIS)',
                'slug' => 'government-erp',
                'code' => 'GOV_ERP',
                'type' => 'web',
                'source' => 'external',
                'icon' => 'building-library',
                'color' => '#0F766E',
                'sort_order' => 12,
                'description' => 'Government financial management system — budget planning, procurement, financial reporting, asset tracking, and audit compliance (NEW GFMIS standard)',
                'modules' => [
                    ['name' => 'Budget Management', 'slug' => 'budget-mgmt', 'code' => 'BUDGET_MGMT', 'description' => 'Annual budget planning, allocation, tracking, and variance analysis per department', 'sort_order' => 1],
                    ['name' => 'Procurement & e-GP', 'slug' => 'procurement', 'code' => 'PROCUREMENT', 'description' => 'e-Government Procurement integration, bidding, vendor selection, purchase orders', 'sort_order' => 2],
                    ['name' => 'Financial Reporting', 'slug' => 'fin-reporting', 'code' => 'FIN_REPORTING', 'description' => 'GFMIS-compliant financial statements, cash flow, trial balance, audit-ready reports', 'sort_order' => 3],
                    ['name' => 'Asset Management', 'slug' => 'asset-mgmt', 'code' => 'ASSET_MGMT', 'description' => 'Government asset registry, depreciation tracking, maintenance schedules, QR tagging', 'sort_order' => 4],
                    ['name' => 'HR & Payroll', 'slug' => 'hr-payroll', 'code' => 'HR_PAYROLL', 'description' => 'Government employee records, payroll processing, leave management, performance reviews', 'sort_order' => 5],
                    ['name' => 'Document Management', 'slug' => 'doc-mgmt', 'code' => 'DOC_MGMT', 'description' => 'e-Document workflow, digital signatures, document tracking, retention policies', 'sort_order' => 6],
                    ['name' => 'Audit & Compliance', 'slug' => 'audit-compliance', 'code' => 'AUDIT_COMPLIANCE', 'description' => 'Internal audit tools, compliance checklists, risk assessment, สตง. audit preparation', 'sort_order' => 7],
                ],
            ],
            [
                'name' => 'Event & MICE',
                'slug' => 'event-mice',
                'code' => 'EVENT_MICE',
                'type' => 'hybrid',
                'source' => 'internal',
                'icon' => 'calendar-days',
                'color' => '#DB2777',
                'sort_order' => 13,
                'description' => 'Complete event management platform — Meetings, Incentives, Conferences, Exhibitions — venue booking, registration, exhibitor management, and analytics',
                'modules' => [
                    ['name' => 'Event Management', 'slug' => 'event-mgmt', 'code' => 'EVENT_MGMT', 'description' => 'Create and manage events — conferences, concerts, festivals, exhibitions, with timeline and task tracking', 'sort_order' => 1],
                    ['name' => 'Venue Booking', 'slug' => 'venue-booking', 'code' => 'VENUE_BOOKING', 'description' => 'Search, compare, and book venues — convention halls, hotels, outdoor spaces, with floor plan viewer', 'sort_order' => 2],
                    ['name' => 'MICE Planner', 'slug' => 'mice-planner', 'code' => 'MICE_PLANNER', 'description' => 'Meetings, Incentives, Conferences, Exhibitions planning tools — budgeting, scheduling, vendor coordination', 'sort_order' => 3],
                    ['name' => 'Ticket & Registration', 'slug' => 'ticket-registration', 'code' => 'TICKET_REG', 'description' => 'Online registration, ticket sales, QR code e-tickets, check-in kiosk integration', 'sort_order' => 4],
                    ['name' => 'Exhibitor Portal', 'slug' => 'exhibitor-portal', 'code' => 'EXHIBITOR_PORTAL', 'description' => 'Exhibitor self-service — booth selection, setup requirements, lead collection, ROI tracking', 'sort_order' => 5],
                    ['name' => 'Attendee Engagement', 'slug' => 'attendee-engagement', 'code' => 'ATTENDEE_ENGAGE', 'description' => 'Mobile event app, agenda builder, networking matchmaking, live Q&A, polling, and gamification', 'sort_order' => 6],
                    ['name' => 'Event Analytics', 'slug' => 'event-analytics', 'code' => 'EVENT_ANALYTICS', 'description' => 'Attendance tracking, engagement metrics, revenue reports, post-event surveys, ROI analysis', 'sort_order' => 7],
                ],
            ],
            [
                'name' => 'Project Management',
                'slug' => 'project-management',
                'code' => 'PROJECT_MGMT',
                'type' => 'web',
                'source' => 'internal',
                'icon' => 'clipboard-document-check',
                'color' => '#7C3AED',
                'sort_order' => 14,
                'description' => 'End-to-end project management — planning, task tracking, Gantt charts, resource allocation, budget control, risk management, and executive reporting',
                'modules' => [
                    ['name' => 'Project Dashboard', 'slug' => 'project-dashboard', 'code' => 'PROJECT_DASH', 'description' => 'Project overview — status, timeline, milestones, progress tracking, KPI scorecards', 'sort_order' => 1],
                    ['name' => 'Task & Gantt', 'slug' => 'task-gantt', 'code' => 'TASK_GANTT', 'description' => 'Task management with Gantt chart, Kanban board, dependencies, assignments, and deadlines', 'sort_order' => 2],
                    ['name' => 'Resource Management', 'slug' => 'resource-mgmt', 'code' => 'RESOURCE_MGMT', 'description' => 'Allocate team members, equipment, and facilities — capacity planning, availability tracking', 'sort_order' => 3],
                    ['name' => 'Budget & Cost Tracking', 'slug' => 'budget-cost', 'code' => 'BUDGET_COST', 'description' => 'Project budgets, expense tracking, cost forecasting, variance analysis, invoice management', 'sort_order' => 4],
                    ['name' => 'Document & Files', 'slug' => 'project-docs', 'code' => 'PROJECT_DOCS', 'description' => 'Project documents, version control, file sharing, approval workflows, template library', 'sort_order' => 5],
                    ['name' => 'Risk Management', 'slug' => 'risk-mgmt', 'code' => 'RISK_MGMT', 'description' => 'Risk identification, impact assessment, mitigation plans, risk register, issue tracking', 'sort_order' => 6],
                    ['name' => 'Reporting & Analytics', 'slug' => 'project-reports', 'code' => 'PROJECT_REPORTS', 'description' => 'Status reports, burn-down charts, performance metrics, executive summaries, export to PDF', 'sort_order' => 7],
                ],
            ],
            [
                'name' => 'Data Exchange',
                'slug' => 'data-exchange',
                'code' => 'DATA_EXCHANGE',
                'type' => 'web',
                'source' => 'internal',
                'icon' => 'circle-stack',
                'color' => '#059669',
                'sort_order' => 15,
                'description' => 'Central data exchange hub — data catalog, quality management, security governance, high-value datasets, sandbox analytics, data requests, and usage monitoring',
                'modules' => [
                    ['name' => 'Data Index', 'slug' => 'data-index', 'code' => 'DATA_INDEX', 'description' => 'Central data directory — search across agencies, metadata registry, cross-reference mapping', 'sort_order' => 1],
                    ['name' => 'Data Catalog', 'slug' => 'data-catalog', 'code' => 'DATA_CATALOG', 'description' => 'Dataset listings with descriptions, schema, tags, ownership, classification, and lineage tracking', 'sort_order' => 2],
                    ['name' => 'Data Quality', 'slug' => 'data-quality', 'code' => 'DATA_QUALITY', 'description' => 'Automated quality checks — completeness, accuracy, consistency, timeliness scoring and alerts', 'sort_order' => 3],
                    ['name' => 'Data Security', 'slug' => 'data-security', 'code' => 'DATA_SECURITY', 'description' => 'Access control, encryption policies, data masking, PII detection, audit trail, compliance reports', 'sort_order' => 4],
                    ['name' => 'High Value Dataset', 'slug' => 'high-value-dataset', 'code' => 'HIGH_VALUE_DS', 'description' => 'Curated high-value open datasets — downloadable formats, API access, usage statistics, citations', 'sort_order' => 5],
                    ['name' => 'Data Sandbox', 'slug' => 'data-sandbox', 'code' => 'DATA_SANDBOX', 'description' => 'Safe analysis environment — Jupyter notebooks, BI tools, sample datasets, isolated compute', 'sort_order' => 6],
                    ['name' => 'Data Request', 'slug' => 'data-request', 'code' => 'DATA_REQUEST', 'description' => 'Request data from agencies — approval workflow, SLA tracking, request history, status dashboard', 'sort_order' => 7],
                    ['name' => 'Tracking & Monitoring', 'slug' => 'data-tracking', 'code' => 'DATA_TRACKING', 'description' => 'Usage analytics, download stats, API call monitoring, compliance dashboards, anomaly alerts', 'sort_order' => 8],
                ],
            ],
        ];

        // Get tourists group for granting access
        $touristGroupId = DB::table('groups')->where('slug', 'tourists')->value('id');

        foreach ($apps as $appData) {
            $modules = $appData['modules'];
            unset($appData['modules']);

            // Skip if app already exists
            if (DB::table('applications')->where('code', $appData['code'])->exists()) {
                continue;
            }

            // Insert application
            $appId = DB::table('applications')->insertGetId(array_merge($appData, [
                'is_active' => true,
                'show_in_menu' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Link to Pattaya cluster
            if ($pattayaId) {
                DB::table('cluster_application')->insert([
                    'cluster_id' => $pattayaId,
                    'application_id' => $appId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Grant tourist group access
            if ($touristGroupId && $pattayaId) {
                DB::table('group_app_access')->insert([
                    'group_id' => $touristGroupId,
                    'application_id' => $appId,
                    'cluster_id' => $pattayaId,
                    'has_access' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Insert modules
            foreach ($modules as $module) {
                DB::table('modules')->insert(array_merge([
                    'application_id' => $appId,
                    'is_active' => true,
                    'is_premium' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $module));
            }
        }

        // ── Add Currency Exchange API provider ──
        if (! DB::table('api_providers')->where('slug', 'currency-exchange')->exists()) {
            DB::table('api_providers')->insert([
                'name' => 'Currency Exchange API',
                'slug' => 'currency-exchange',
                'category' => 'currency',
                'description' => 'Real-time currency exchange rates and conversion for multi-country tourism transactions',
                'base_url' => null,
                'docs_url' => null,
                'adapter_class' => null,
                'is_active' => true,
                'is_shared' => true,
                'supported_countries' => null,
                'default_config' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('api_providers')->where('slug', 'currency-exchange')->delete();

        $codes = ['CITY_DASHBOARD', 'GOV_ERP', 'EVENT_MICE', 'PROJECT_MGMT', 'DATA_EXCHANGE'];

        foreach ($codes as $code) {
            $appId = DB::table('applications')->where('code', $code)->value('id');
            if ($appId) {
                DB::table('modules')->where('application_id', $appId)->delete();
                DB::table('group_app_access')->where('application_id', $appId)->delete();
                DB::table('cluster_application')->where('application_id', $appId)->delete();
                DB::table('applications')->where('id', $appId)->delete();
            }
        }
    }
};
