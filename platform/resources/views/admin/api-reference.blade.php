@extends('layouts.admin')

@section('title', 'API Reference')

@section('content')
    <x-admin.breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
        ['label' => 'API Reference'],
    ]" />

    {{-- Header --}}
    <div class="mt-4 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">API Reference</h1>
                <p class="mt-1 text-sm text-gray-500">
                    Complete API documentation with interactive sandbox for testing.
                </p>
                <p class="text-sm text-gray-400">
                    เอกสาร API ฉบับสมบูรณ์พร้อม Sandbox สำหรับทดสอบการเชื่อมต่อ
                </p>
            </div>
            <div x-data="{ lang: 'en' }" class="flex items-center gap-2">
                <span class="text-xs text-gray-400">Language:</span>
                <button @click="$dispatch('set-lang', { lang: 'en' })"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
                        :class="'bg-blue-100 text-blue-700'">
                    EN
                </button>
                <button @click="$dispatch('set-lang', { lang: 'th' })"
                        class="px-3 py-1 text-xs font-medium rounded-full transition-colors"
                        :class="'bg-gray-100 text-gray-600'">
                    TH
                </button>
            </div>
        </div>

        {{-- Base URL --}}
        <div class="mt-4 flex items-center gap-2 bg-gray-50 rounded-lg px-4 py-2">
            <span class="text-xs font-medium text-gray-500">Base URL:</span>
            <code class="text-sm font-mono text-gray-800">{{ url('/api') }}</code>
        </div>

        {{-- Quick nav --}}
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="#auth" class="text-xs px-3 py-1 bg-green-50 text-green-700 rounded-full hover:bg-green-100">Authentication</a>
            <a href="#clusters" class="text-xs px-3 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100">Clusters & Countries</a>
            <a href="#menu" class="text-xs px-3 py-1 bg-purple-50 text-purple-700 rounded-full hover:bg-purple-100">Super App Menu</a>
            <a href="#admin-dashboard" class="text-xs px-3 py-1 bg-orange-50 text-orange-700 rounded-full hover:bg-orange-100">Admin Dashboard</a>
            <a href="#permissions" class="text-xs px-3 py-1 bg-red-50 text-red-700 rounded-full hover:bg-red-100">Permissions</a>
        </div>
    </div>

    {{-- Authentication section --}}
    <div id="auth" class="mt-8">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Authentication</h2>
        <p class="text-sm text-gray-500 mb-4">ระบบยืนยันตัวตน (SSO) — รองรับทั้ง Email/Password และ Social Login</p>

        {{-- POST /auth/login --}}
        <x-admin.api-endpoint
            method="POST"
            path="/api/auth/login"
            auth="false"
            title-en="User Login"
            title-th="เข้าสู่ระบบด้วยอีเมล"
            desc-en="Authenticate a user with email and password. Returns a Bearer token for subsequent API calls. The token is a Laravel Sanctum personal access token."
            desc-th="ยืนยันตัวตนผู้ใช้ด้วยอีเมลและรหัสผ่าน ส่งคืน Bearer token สำหรับเรียก API ถัดไป Token เป็น Laravel Sanctum personal access token"
            :params="[
                ['name' => 'email', 'type' => 'string', 'required' => true, 'desc_en' => 'User email address', 'desc_th' => 'อีเมลผู้ใช้'],
                ['name' => 'password', 'type' => 'string', 'required' => true, 'desc_en' => 'User password', 'desc_th' => 'รหัสผ่านผู้ใช้'],
                ['name' => 'device_name', 'type' => 'string', 'required' => false, 'desc_en' => 'Device name for token identification (max 100 chars)', 'desc_th' => 'ชื่ออุปกรณ์สำหรับระบุ token (สูงสุด 100 ตัวอักษร)'],
            ]"
            :sample-body='json_encode(["email" => "admin@example.com", "password" => "password", "device_name" => "web-browser"], JSON_PRETTY_PRINT)'
            :sample-response='json_encode(["token" => "1|abc123...", "token_type" => "Bearer", "session" => ["user" => ["id" => 1, "name" => "Admin", "email" => "admin@example.com"], "accessible_clusters" => [1, 2], "access_map" => ["1" => [1, 2, 3]]]], JSON_PRETTY_PRINT)'
        />

        {{-- POST /auth/sso --}}
        <x-admin.api-endpoint
            method="POST"
            path="/api/auth/sso"
            auth="false"
            title-en="SSO / Social Login"
            title-th="เข้าสู่ระบบด้วย Social Login (SSO)"
            desc-en="Authenticate via external SSO providers (Google, Facebook, LINE, Apple). Creates a new user account if not found. Returns a Bearer token and session info."
            desc-th="ยืนยันตัวตนผ่าน SSO provider ภายนอก (Google, Facebook, LINE, Apple) สร้างบัญชีใหม่อัตโนมัติหากไม่พบ ส่งคืน Bearer token และข้อมูล session"
            :params="[
                ['name' => 'provider', 'type' => 'string', 'required' => true, 'desc_en' => 'SSO provider: google, facebook, line, apple', 'desc_th' => 'ผู้ให้บริการ SSO: google, facebook, line, apple'],
                ['name' => 'provider_id', 'type' => 'string', 'required' => true, 'desc_en' => 'Unique user ID from the provider', 'desc_th' => 'ID ผู้ใช้จาก provider'],
                ['name' => 'name', 'type' => 'string', 'required' => false, 'desc_en' => 'User display name', 'desc_th' => 'ชื่อแสดงผลของผู้ใช้'],
                ['name' => 'email', 'type' => 'string', 'required' => false, 'desc_en' => 'User email from provider', 'desc_th' => 'อีเมลจาก provider'],
                ['name' => 'phone', 'type' => 'string', 'required' => false, 'desc_en' => 'User phone number', 'desc_th' => 'เบอร์โทรศัพท์'],
                ['name' => 'avatar', 'type' => 'string', 'required' => false, 'desc_en' => 'URL to user avatar', 'desc_th' => 'URL รูปโปรไฟล์'],
                ['name' => 'device_name', 'type' => 'string', 'required' => false, 'desc_en' => 'Device name for token', 'desc_th' => 'ชื่ออุปกรณ์'],
            ]"
            :sample-body='json_encode(["provider" => "google", "provider_id" => "1234567890", "name" => "John Doe", "email" => "john@gmail.com"], JSON_PRETTY_PRINT)'
            :sample-response='json_encode(["token" => "2|xyz789...", "token_type" => "Bearer", "session" => ["user" => ["id" => 5, "name" => "John Doe", "email" => "john@gmail.com", "sso_provider" => "google"]]], JSON_PRETTY_PRINT)'
        />

        {{-- GET /auth/session --}}
        <x-admin.api-endpoint
            method="GET"
            path="/api/auth/session"
            auth="true"
            title-en="Get Current Session"
            title-th="ดึงข้อมูล Session ปัจจุบัน"
            desc-en="Returns the current authenticated user's profile, accessible clusters, and permission access map. Optionally scoped to a cluster via X-Cluster-Id header."
            desc-th="ส่งคืนข้อมูลโปรไฟล์ผู้ใช้ที่ล็อกอินอยู่ รายการ Cluster ที่เข้าถึงได้ และแผนที่สิทธิ์การเข้าถึง สามารถระบุ Cluster ผ่าน X-Cluster-Id header ได้"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => false],
            ]"
            :sample-response='json_encode(["data" => ["user" => ["id" => 1, "name" => "Admin", "email" => "admin@example.com", "locale" => "th", "status" => "active"], "accessible_clusters" => [1], "access_map" => ["1" => [1, 2, 3, 4, 5], "2" => [6, 7, 8]]]], JSON_PRETTY_PRINT)'
        />

        {{-- POST /auth/logout --}}
        <x-admin.api-endpoint
            method="POST"
            path="/api/auth/logout"
            auth="true"
            title-en="Logout"
            title-th="ออกจากระบบ"
            desc-en="Revokes the current access token. The user must re-authenticate to get a new token."
            desc-th="เพิกถอน access token ปัจจุบัน ผู้ใช้ต้องยืนยันตัวตนใหม่เพื่อรับ token ใหม่"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
            ]"
            :sample-response='json_encode(["message" => "Logged out successfully"], JSON_PRETTY_PRINT)'
        />
    </div>

    {{-- Clusters & Countries section --}}
    <div id="clusters" class="mt-10">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Clusters & Countries</h2>
        <p class="text-sm text-gray-500 mb-4">ข้อมูลประเทศ, Cluster (เมือง/ปลายทาง) และแอปที่เปิดใช้งาน</p>

        {{-- GET /countries --}}
        <x-admin.api-endpoint
            method="GET"
            path="/api/countries"
            auth="false"
            title-en="List Countries"
            title-th="รายการประเทศทั้งหมด"
            desc-en="Returns all active countries with their active clusters. Public endpoint, no authentication needed. Use this to build a destination selector UI."
            desc-th="ส่งคืนรายการประเทศที่เปิดใช้งานพร้อม Cluster ที่ active ไม่ต้องยืนยันตัวตน ใช้สร้าง UI เลือกปลายทาง"
            :sample-response='json_encode(["data" => [["id" => 1, "name" => "Thailand", "code" => "THA", "code_alpha2" => "TH", "currency_code" => "THB", "timezone" => "Asia/Bangkok", "clusters" => [["id" => 1, "name" => "Pattaya", "slug" => "pattaya", "code" => "PTY", "is_active" => true]]]]], JSON_PRETTY_PRINT)'
        />

        {{-- GET /clusters/accessible --}}
        <x-admin.api-endpoint
            method="GET"
            path="/api/clusters/accessible"
            auth="true"
            title-en="List Accessible Clusters"
            title-th="รายการ Cluster ที่ผู้ใช้เข้าถึงได้"
            desc-en="Returns only clusters that the current user has permission to access, based on their roles, group memberships, and explicit access grants."
            desc-th="ส่งคืนเฉพาะ Cluster ที่ผู้ใช้ปัจจุบันมีสิทธิ์เข้าถึง ตามบทบาท กลุ่มสมาชิก และสิทธิ์ที่กำหนดให้"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
            ]"
            :sample-response='json_encode(["data" => [["id" => 1, "name" => "Pattaya", "slug" => "pattaya", "code" => "PTY", "country" => ["id" => 1, "name" => "Thailand"]]]], JSON_PRETTY_PRINT)'
        />

        {{-- GET /clusters/{clusterId} --}}
        <x-admin.api-endpoint
            method="GET"
            path="/api/clusters/{clusterId}"
            auth="true"
            title-en="Cluster Detail with Apps"
            title-th="รายละเอียด Cluster พร้อมแอปที่เข้าถึงได้"
            desc-en="Returns detailed information about a specific cluster including all applications and their modules that the current user can access. Requires cluster context."
            desc-th="ส่งคืนรายละเอียดของ Cluster พร้อมแอปพลิเคชันและโมดูลทั้งหมดที่ผู้ใช้ปัจจุบันเข้าถึงได้ ต้องระบุ cluster context"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '{clusterId}', 'required' => true],
            ]"
            :params="[
                ['name' => 'clusterId', 'type' => 'integer', 'required' => true, 'desc_en' => 'Cluster ID (path parameter)', 'desc_th' => 'ID ของ Cluster (path parameter)'],
            ]"
            :sample-response='json_encode(["data" => ["cluster" => ["id" => 1, "name" => "Pattaya", "slug" => "pattaya", "country_id" => 1], "applications" => [["id" => 1, "name" => "App Together", "code" => "APP_TOGETHER", "icon" => "compass", "modules" => [["id" => 1, "name" => "Explore", "code" => "EXPLORE"]]]]]], JSON_PRETTY_PRINT)'
        />
    </div>

    {{-- Super App Menu section --}}
    <div id="menu" class="mt-10">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Super App Menu</h2>
        <p class="text-sm text-gray-500 mb-4">เมนูนำทาง Dynamic สำหรับ Global Header — กรองตามสิทธิ์ผู้ใช้อัตโนมัติ</p>

        {{-- GET /menu --}}
        <x-admin.api-endpoint
            method="GET"
            path="/api/menu"
            auth="true"
            title-en="Get Dynamic Menu"
            title-th="ดึงเมนูนำทางแบบ Dynamic"
            desc-en="Returns the global header menu items filtered by the current user's permissions, visibility rules, and cluster context. Menu items linked to applications are only shown if the user has access. Supports hierarchical parent-child menu structure."
            desc-th="ส่งคืนรายการเมนู Global Header ที่กรองตามสิทธิ์ผู้ใช้ กฎ visibility และ cluster context เมนูที่ผูกกับแอปจะแสดงเฉพาะเมื่อผู้ใช้มีสิทธิ์เข้าถึง รองรับโครงสร้างเมนูแบบ parent-child"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
            ]"
            :sample-response='json_encode(["data" => ["menu" => [["id" => 1, "label" => "Home", "icon" => "home", "url" => "/", "visibility" => "all", "children" => []], ["id" => 2, "label" => "Explore", "icon" => "compass", "url" => "/explore", "visibility" => "all", "children" => []]], "cluster" => ["id" => 1, "name" => "Pattaya", "slug" => "pattaya"], "country" => ["id" => 1, "name" => "Thailand", "code" => "THA"]]], JSON_PRETTY_PRINT)'
        />
    </div>

    {{-- Admin Dashboard section --}}
    <div id="admin-dashboard" class="mt-10">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Admin Dashboard</h2>
        <p class="text-sm text-gray-500 mb-4">ข้อมูลภาพรวมระบบ — แสดงผลตามระดับสิทธิ์ผู้ดูแล (Global / Country / Cluster)</p>

        {{-- GET /admin/api/dashboard --}}
        <x-admin.api-endpoint
            method="GET"
            path="/admin/api/dashboard"
            auth="true"
            title-en="Admin Dashboard Overview"
            title-th="ภาพรวม Dashboard สำหรับผู้ดูแลระบบ"
            desc-en="Returns dashboard statistics based on the admin's level. Global admins see all countries/clusters/users counts. Country admins see their country's clusters. Cluster admins see their cluster's applications."
            desc-th="ส่งคืนสถิติ Dashboard ตามระดับสิทธิ์ผู้ดูแล Global Admin เห็นข้อมูลทุกประเทศ/Cluster/ผู้ใช้ Country Admin เห็นเฉพาะ Cluster ในประเทศ Cluster Admin เห็นเฉพาะแอปใน Cluster"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
            ]"
            :sample-response='json_encode(["data" => ["admin_level" => "global", "countries" => 2, "clusters" => 3, "total_users" => 150, "applications" => 10, "api_providers" => 8, "countries_list" => [["id" => 1, "name" => "Thailand", "code" => "THA", "active_clusters_count" => 2]]]], JSON_PRETTY_PRINT)'
        />
    </div>

    {{-- Permissions section --}}
    <div id="permissions" class="mt-10">
        <h2 class="text-lg font-bold text-gray-900 mb-1">Permission Management</h2>
        <p class="text-sm text-gray-500 mb-4">จัดการสิทธิ์การเข้าถึงแอป/โมดูลสำหรับผู้ใช้และกลุ่ม — ระบบ 5 ระดับลำดับชั้น</p>

        {{-- GET /admin/api/permissions/groups --}}
        <x-admin.api-endpoint
            method="GET"
            path="/admin/api/permissions/groups"
            auth="true"
            title-en="List Permission Groups"
            title-th="รายการกลุ่มสิทธิ์ทั้งหมด"
            desc-en="Returns all permission groups with their user counts. Can be filtered by cluster_id or country_id via query parameters."
            desc-th="ส่งคืนรายการกลุ่มสิทธิ์ทั้งหมดพร้อมจำนวนสมาชิก สามารถกรองตาม cluster_id หรือ country_id ผ่าน query parameter"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
            ]"
            :params="[
                ['name' => 'cluster_id', 'type' => 'integer', 'required' => false, 'desc_en' => 'Filter by cluster ID', 'desc_th' => 'กรองตาม Cluster ID'],
                ['name' => 'country_id', 'type' => 'integer', 'required' => false, 'desc_en' => 'Filter by country ID', 'desc_th' => 'กรองตาม Country ID'],
            ]"
            :sample-response='json_encode(["data" => [["id" => 1, "name" => "Operators", "slug" => "operators", "scope" => "global", "description" => "System operators and staff", "users_count" => 5], ["id" => 2, "name" => "Merchants", "slug" => "merchants", "scope" => "cluster", "description" => "Pattaya merchants", "users_count" => 42]]], JSON_PRETTY_PRINT)'
        />

        {{-- POST /admin/api/permissions/user-access --}}
        <x-admin.api-endpoint
            method="POST"
            path="/admin/api/permissions/user-access"
            auth="true"
            title-en="Set User App & Module Access"
            title-th="กำหนดสิทธิ์แอปและโมดูลสำหรับผู้ใช้"
            desc-en="Sets which applications and modules a specific user can access within a specific cluster. Replaces existing access for the given cluster. This is the most specific permission level (user-specific override)."
            desc-th="กำหนดว่าผู้ใช้สามารถเข้าถึงแอปและโมดูลใดภายใน Cluster ที่กำหนด แทนที่สิทธิ์เดิมของ Cluster นั้น เป็นสิทธิ์ระดับเฉพาะเจาะจงที่สุด (user-specific override)"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
            ]"
            :params="[
                ['name' => 'user_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Target user ID', 'desc_th' => 'ID ผู้ใช้เป้าหมาย'],
                ['name' => 'cluster_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Cluster ID to scope access', 'desc_th' => 'Cluster ID ที่จะกำหนดสิทธิ์'],
                ['name' => 'access', 'type' => 'array', 'required' => true, 'desc_en' => 'Array of {application_id, module_ids[]} pairs', 'desc_th' => 'อาร์เรย์คู่ {application_id, module_ids[]}'],
            ]"
            :sample-body='json_encode(["user_id" => 5, "cluster_id" => 1, "access" => [["application_id" => 1, "module_ids" => [1, 2, 4]], ["application_id" => 3, "module_ids" => [1, 5, 8]]]], JSON_PRETTY_PRINT)'
            :sample-response='json_encode(["message" => "Access updated successfully"], JSON_PRETTY_PRINT)'
        />

        {{-- POST /admin/api/permissions/group-access --}}
        <x-admin.api-endpoint
            method="POST"
            path="/admin/api/permissions/group-access"
            auth="true"
            title-en="Set Group App & Module Access"
            title-th="กำหนดสิทธิ์แอปและโมดูลสำหรับกลุ่ม"
            desc-en="Sets which applications and modules all users in a specific group can access within a cluster. All group members inherit these permissions unless overridden by user-specific access."
            desc-th="กำหนดว่าสมาชิกในกลุ่มสามารถเข้าถึงแอปและโมดูลใดภายใน Cluster สมาชิกทุกคนจะได้รับสิทธิ์นี้ ยกเว้นถูก override ด้วยสิทธิ์ระดับผู้ใช้"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
            ]"
            :params="[
                ['name' => 'group_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Target group ID', 'desc_th' => 'ID กลุ่มเป้าหมาย'],
                ['name' => 'cluster_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Cluster ID to scope access', 'desc_th' => 'Cluster ID ที่จะกำหนดสิทธิ์'],
                ['name' => 'access', 'type' => 'array', 'required' => true, 'desc_en' => 'Array of {application_id, module_ids[]} pairs', 'desc_th' => 'อาร์เรย์คู่ {application_id, module_ids[]}'],
            ]"
            :sample-body='json_encode(["group_id" => 3, "cluster_id" => 1, "access" => [["application_id" => 1, "module_ids" => [1, 2, 3, 4, 5, 6, 7, 8]]]], JSON_PRETTY_PRINT)'
            :sample-response='json_encode(["message" => "Group access updated successfully"], JSON_PRETTY_PRINT)'
        />

        {{-- GET /admin/api/permissions/user-access-map/{userId} --}}
        <x-admin.api-endpoint
            method="GET"
            path="/admin/api/permissions/user-access-map/{userId}"
            auth="true"
            title-en="Get User Access Map"
            title-th="ดึงแผนที่สิทธิ์การเข้าถึงของผู้ใช้"
            desc-en="Returns the complete access map for a user, showing which applications and modules they can access in each cluster. Optionally filter by cluster_id."
            desc-th="ส่งคืนแผนที่สิทธิ์ทั้งหมดของผู้ใช้ แสดงแอปและโมดูลที่เข้าถึงได้ในแต่ละ Cluster สามารถกรองตาม cluster_id"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
            ]"
            :params="[
                ['name' => 'userId', 'type' => 'integer', 'required' => true, 'desc_en' => 'Target user ID (path parameter)', 'desc_th' => 'ID ผู้ใช้เป้าหมาย (path parameter)'],
                ['name' => 'cluster_id', 'type' => 'integer', 'required' => false, 'desc_en' => 'Filter by cluster ID (query)', 'desc_th' => 'กรองตาม Cluster ID (query)'],
            ]"
            :sample-response='json_encode(["data" => ["1" => ["1" => [1, 2, 3, 4, 5], "3" => [1, 5, 8]]]], JSON_PRETTY_PRINT)'
        />

        {{-- POST /admin/api/permissions/assign-role --}}
        <x-admin.api-endpoint
            method="POST"
            path="/admin/api/permissions/assign-role"
            auth="true"
            title-en="Assign Role to User"
            title-th="กำหนดบทบาทให้ผู้ใช้"
            desc-en="Assigns a role to a user with optional country/cluster scope. Global roles require no scope. Country roles require country_id. Cluster roles require cluster_id."
            desc-th="กำหนดบทบาทให้ผู้ใช้พร้อมขอบเขต (scope) ที่เลือกได้ บทบาท Global ไม่ต้องระบุ scope บทบาท Country ต้องระบุ country_id บทบาท Cluster ต้องระบุ cluster_id"
            :headers="[
                ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
            ]"
            :params="[
                ['name' => 'user_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Target user ID', 'desc_th' => 'ID ผู้ใช้เป้าหมาย'],
                ['name' => 'role_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Role ID to assign', 'desc_th' => 'ID บทบาทที่จะกำหนด'],
                ['name' => 'country_id', 'type' => 'integer', 'required' => false, 'desc_en' => 'Country scope (for country-level roles)', 'desc_th' => 'ขอบเขตประเทศ (สำหรับบทบาทระดับประเทศ)'],
                ['name' => 'cluster_id', 'type' => 'integer', 'required' => false, 'desc_en' => 'Cluster scope (for cluster-level roles)', 'desc_th' => 'ขอบเขต Cluster (สำหรับบทบาทระดับ Cluster)'],
            ]"
            :sample-body='json_encode(["user_id" => 5, "role_id" => 3, "cluster_id" => 1], JSON_PRETTY_PRINT)'
            :sample-response='json_encode(["message" => "Role assigned successfully"], JSON_PRETTY_PRINT)'
        />
    </div>

    {{-- Permission hierarchy info --}}
    <div class="mt-10 mb-8">
        <x-ui.card title="Permission Resolution / ลำดับการตรวจสอบสิทธิ์">
            <div class="text-sm text-gray-600 space-y-3">
                <p class="font-medium text-gray-900">Resolution Order (Most specific wins):</p>
                <ol class="list-decimal list-inside space-y-1 ml-2">
                    <li><strong>Step 1:</strong> Authenticate via SSO token / ยืนยันตัวตนผ่าน SSO token</li>
                    <li><strong>Step 2:</strong> Check Country-level permission / ตรวจสิทธิ์ระดับประเทศ</li>
                    <li><strong>Step 3:</strong> Check Cluster-level permission / ตรวจสิทธิ์ระดับ Cluster</li>
                    <li><strong>Step 4:</strong> Check App-level permission / ตรวจสิทธิ์ระดับแอป</li>
                    <li><strong>Step 5:</strong> Check Module-level permission / ตรวจสิทธิ์ระดับโมดูล</li>
                    <li><strong>Step 6:</strong> Check Group overrides / ตรวจสิทธิ์กลุ่ม</li>
                    <li><strong>Step 7:</strong> Check User-specific overrides / ตรวจสิทธิ์เฉพาะผู้ใช้</li>
                </ol>

                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="font-medium text-blue-800">Cluster Context / การระบุ Cluster:</p>
                    <p class="text-blue-700 mt-1">
                        The system resolves cluster context in order: <code class="bg-blue-100 px-1 rounded">X-Cluster-Id</code> header
                        &rarr; <code class="bg-blue-100 px-1 rounded">cluster</code> route/query parameter
                        &rarr; subdomain &rarr; default (dev only)
                    </p>
                    <p class="text-blue-700 mt-1">
                        ระบบจะหา Cluster ตามลำดับ: <code class="bg-blue-100 px-1 rounded">X-Cluster-Id</code> header
                        &rarr; <code class="bg-blue-100 px-1 rounded">cluster</code> route/query parameter
                        &rarr; subdomain &rarr; ค่าเริ่มต้น (dev เท่านั้น)
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection
