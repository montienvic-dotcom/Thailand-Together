@extends('layouts.superapp')

@section('title', 'API Reference - Thailand Together')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h1 class="text-2xl font-bold text-gray-900">API Reference</h1>
            <p class="mt-1 text-sm text-gray-500">
                Complete REST API documentation for the Thailand Together platform.
            </p>
            <p class="text-sm text-gray-400">
                เอกสาร REST API ฉบับสมบูรณ์สำหรับ Thailand Together Platform
            </p>

            {{-- Base URL --}}
            <div class="mt-4 flex items-center gap-2 bg-gray-50 rounded-lg px-4 py-2">
                <span class="text-xs font-medium text-gray-500">Base URL:</span>
                <code class="text-sm font-mono text-gray-800">{{ url('/api') }}</code>
            </div>

            {{-- Quick nav --}}
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="#platform" class="text-xs px-3 py-1 bg-gray-50 text-gray-700 rounded-full hover:bg-gray-100">Platform Overview</a>
                <a href="#auth" class="text-xs px-3 py-1 bg-green-50 text-green-700 rounded-full hover:bg-green-100">Authentication</a>
                <a href="#clusters" class="text-xs px-3 py-1 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100">Clusters & Countries</a>
                <a href="#menu" class="text-xs px-3 py-1 bg-purple-50 text-purple-700 rounded-full hover:bg-purple-100">Super App Menu</a>
                <a href="#admin" class="text-xs px-3 py-1 bg-orange-50 text-orange-700 rounded-full hover:bg-orange-100">Admin APIs</a>
                <a href="#integrations" class="text-xs px-3 py-1 bg-teal-50 text-teal-700 rounded-full hover:bg-teal-100">Third-Party Integrations</a>
            </div>
        </div>

        {{-- Platform Overview --}}
        <div id="platform" class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Platform Overview</h2>

            {{-- Architecture --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 mb-2">Three Pillars Architecture</h3>
                <p class="text-sm text-gray-500 mb-4">สถาปัตยกรรม 3 เสาหลักของ Thailand Together Platform</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-100">
                        <div class="flex items-center gap-2 mb-2">
                            <x-icon name="device-phone-mobile" class="w-5 h-5 text-orange-600" />
                            <h4 class="font-semibold text-gray-900">Pillar 1: App Together</h4>
                        </div>
                        <p class="text-xs text-gray-600">Mobile Super App for tourists — explore, book, navigate, earn rewards, social features, and more. Cross-cluster enabled.</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <div class="flex items-center gap-2 mb-2">
                            <x-icon name="server" class="w-5 h-5 text-blue-600" />
                            <h4 class="font-semibold text-gray-900">Pillar 2: Supporting Systems</h4>
                        </div>
                        <p class="text-xs text-gray-600">Laravel-based backend: Admin Panel, Hotel Management, Tour Booking, Marketplace, CRM, HelpDesk, City Dashboard, Government ERP (GFMIS), Event & MICE, Project Management, Data Exchange, and more.</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
                        <div class="flex items-center gap-2 mb-2">
                            <x-icon name="arrow-path" class="w-5 h-5 text-purple-600" />
                            <h4 class="font-semibold text-gray-900">Pillar 3: Third-Party APIs</h4>
                        </div>
                        <p class="text-xs text-gray-600">Payment Gateway, SMS, AI Call Center, Translation, TTS, Cloud Points, Data Exchange, HelpDesk API.</p>
                    </div>
                </div>
            </div>

            {{-- Hierarchy --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 mb-2">5-Level Hierarchy</h3>
                <p class="text-sm text-gray-500 mb-4">ลำดับชั้น 5 ระดับของระบบ Multi-Country, Multi-Cluster</p>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3">
                        <span class="text-xs font-bold text-white bg-gray-800 rounded px-2 py-0.5">L0</span>
                        <span class="font-medium text-sm text-gray-900">Global</span>
                        <span class="text-xs text-gray-500">— Thailand Together Platform (Central)</span>
                    </div>
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3 ml-4">
                        <span class="text-xs font-bold text-white bg-blue-600 rounded px-2 py-0.5">L1</span>
                        <span class="font-medium text-sm text-gray-900">Country</span>
                        <span class="text-xs text-gray-500">— Thailand, Vietnam, ...</span>
                    </div>
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3 ml-8">
                        <span class="text-xs font-bold text-white bg-green-600 rounded px-2 py-0.5">L2</span>
                        <span class="font-medium text-sm text-gray-900">Cluster</span>
                        <span class="text-xs text-gray-500">— Pattaya, Danang, Chiang Mai, ...</span>
                    </div>
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3 ml-12">
                        <span class="text-xs font-bold text-white bg-orange-600 rounded px-2 py-0.5">L3</span>
                        <span class="font-medium text-sm text-gray-900">Application</span>
                        <span class="text-xs text-gray-500">— App Together, Hotel Mgmt, Tour Booking, City Dashboard, Gov ERP, Event & MICE, Project Mgmt, Data Exchange, ...</span>
                    </div>
                    <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-4 py-3 ml-16">
                        <span class="text-xs font-bold text-white bg-purple-600 rounded px-2 py-0.5">L4</span>
                        <span class="font-medium text-sm text-gray-900">Module</span>
                        <span class="text-xs text-gray-500">— Booking, Map, Chat, Rewards, ...</span>
                    </div>
                </div>
            </div>

            {{-- SSO & Permission --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="text-base font-bold text-gray-900 mb-2">SSO & Permission System</h3>
                <p class="text-sm text-gray-500 mb-4">ระบบ Single Sign-On และสิทธิ์การเข้าถึง 7 ขั้นตอน</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-blue-900 mb-2">Permission Resolution Order (Most specific wins):</p>
                    <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800">
                        <li>Authenticate via SSO token / ยืนยันตัวตนผ่าน SSO</li>
                        <li>Check Country-level permission / ตรวจสิทธิ์ระดับประเทศ</li>
                        <li>Check Cluster-level permission / ตรวจสิทธิ์ระดับ Cluster</li>
                        <li>Check App-level permission / ตรวจสิทธิ์ระดับแอป</li>
                        <li>Check Module-level permission / ตรวจสิทธิ์ระดับโมดูล</li>
                        <li>Check Group overrides / ตรวจสิทธิ์กลุ่ม</li>
                        <li>Check User-specific overrides / ตรวจสิทธิ์เฉพาะผู้ใช้</li>
                    </ol>
                </div>
            </div>

            {{-- XaaS Model --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-2">Service Model (XaaS)</h3>
                <p class="text-sm text-gray-500 mb-4">โมเดลการให้บริการแบบ Everything-as-a-Service</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-sm font-bold text-gray-900">SaaS</p>
                        <p class="text-xs text-gray-500 mt-1">App Together, Admin Panel, Hotel Mgmt, CRM, City Dashboard, Gov ERP, Event & MICE, Project Mgmt, Data Exchange (per cluster)</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-sm font-bold text-gray-900">IaaS</p>
                        <p class="text-xs text-gray-500 mt-1">Servers, Storage, Network, CDN, DNS per cluster</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-sm font-bold text-gray-900">CaaS</p>
                        <p class="text-xs text-gray-500 mt-1">Docker, K8s, Service Mesh, Auto-scale</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <p class="text-sm font-bold text-gray-900">DaaS</p>
                        <p class="text-xs text-gray-500 mt-1">Central Data Lake, BI/Analytics, ML/AI, Reports</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Authentication APIs --}}
        <div id="auth" class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Authentication</h2>
            <p class="text-sm text-gray-500 mb-4">ระบบยืนยันตัวตน (SSO) — รองรับ Email/Password, Google, Facebook, LINE, Apple</p>

            <x-admin.api-endpoint
                method="POST"
                path="/api/auth/login"
                auth="false"
                title-en="User Login"
                title-th="เข้าสู่ระบบด้วยอีเมล"
                desc-en="Authenticate with email and password. Returns a Bearer token for subsequent API calls."
                desc-th="ยืนยันตัวตนด้วยอีเมลและรหัสผ่าน ส่งคืน Bearer token สำหรับเรียก API ถัดไป"
                :params="[
                    ['name' => 'email', 'type' => 'string', 'required' => true, 'desc_en' => 'User email address', 'desc_th' => 'อีเมลผู้ใช้'],
                    ['name' => 'password', 'type' => 'string', 'required' => true, 'desc_en' => 'User password', 'desc_th' => 'รหัสผ่าน'],
                    ['name' => 'device_name', 'type' => 'string', 'required' => false, 'desc_en' => 'Device name for token identification', 'desc_th' => 'ชื่ออุปกรณ์'],
                ]"
                :sample-body='json_encode(["email" => "admin@thailandtogether.com", "password" => "password", "device_name" => "web-browser"], JSON_PRETTY_PRINT)'
                :sample-response='json_encode(["token" => "1|abc123...", "token_type" => "Bearer", "session" => ["user" => ["id" => 1, "name" => "Admin", "email" => "admin@thailandtogether.com"], "accessible_clusters" => [1], "access_map" => ["1" => [1, 2, 3]]]], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="POST"
                path="/api/auth/sso"
                auth="false"
                title-en="SSO / Social Login"
                title-th="เข้าสู่ระบบด้วย Social Login (SSO)"
                desc-en="Authenticate via external SSO providers (Google, Facebook, LINE, Apple). Auto-creates user if not found."
                desc-th="ยืนยันตัวตนผ่าน SSO provider ภายนอก สร้างบัญชีใหม่อัตโนมัติหากไม่พบ"
                :params="[
                    ['name' => 'provider', 'type' => 'string', 'required' => true, 'desc_en' => 'SSO provider: google, facebook, line, apple', 'desc_th' => 'ผู้ให้บริการ SSO'],
                    ['name' => 'provider_id', 'type' => 'string', 'required' => true, 'desc_en' => 'Unique user ID from provider', 'desc_th' => 'ID ผู้ใช้จาก provider'],
                    ['name' => 'name', 'type' => 'string', 'required' => false, 'desc_en' => 'User display name', 'desc_th' => 'ชื่อแสดงผล'],
                    ['name' => 'email', 'type' => 'string', 'required' => false, 'desc_en' => 'User email', 'desc_th' => 'อีเมล'],
                ]"
                :sample-body='json_encode(["provider" => "google", "provider_id" => "1234567890", "name" => "John Doe", "email" => "john@gmail.com"], JSON_PRETTY_PRINT)'
                :sample-response='json_encode(["token" => "2|xyz789...", "token_type" => "Bearer", "session" => ["user" => ["id" => 5, "name" => "John Doe", "sso_provider" => "google"]]], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="GET"
                path="/api/auth/session"
                auth="true"
                title-en="Get Current Session"
                title-th="ดึงข้อมูล Session ปัจจุบัน"
                desc-en="Returns authenticated user profile, accessible clusters, and permission access map."
                desc-th="ส่งคืนโปรไฟล์ผู้ใช้, Cluster ที่เข้าถึงได้, และแผนที่สิทธิ์"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                    ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => false],
                ]"
                :sample-response='json_encode(["data" => ["user" => ["id" => 1, "name" => "Admin", "email" => "admin@thailandtogether.com"], "accessible_clusters" => [1], "access_map" => ["1" => [1, 2, 3, 4, 5]]]], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="POST"
                path="/api/auth/logout"
                auth="true"
                title-en="Logout"
                title-th="ออกจากระบบ"
                desc-en="Revokes the current access token."
                desc-th="เพิกถอน access token ปัจจุบัน"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ]"
                :sample-response='json_encode(["message" => "Logged out successfully"], JSON_PRETTY_PRINT)'
            />
        </div>

        {{-- Clusters & Countries --}}
        <div id="clusters" class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Clusters & Countries</h2>
            <p class="text-sm text-gray-500 mb-4">ข้อมูลประเทศ, Cluster (เมือง/ปลายทาง) และแอปที่เปิดใช้งาน</p>

            <x-admin.api-endpoint
                method="GET"
                path="/api/countries"
                auth="false"
                title-en="List Countries"
                title-th="รายการประเทศทั้งหมด"
                desc-en="Returns all active countries with their active clusters. Public endpoint."
                desc-th="ส่งคืนรายการประเทศที่เปิดใช้งานพร้อม Cluster — ไม่ต้องยืนยันตัวตน"
                :sample-response='json_encode(["data" => [["id" => 1, "name" => "Thailand", "code" => "THA", "currency_code" => "THB", "clusters" => [["id" => 1, "name" => "Pattaya", "slug" => "pattaya"]]]]], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="GET"
                path="/api/clusters/accessible"
                auth="true"
                title-en="List Accessible Clusters"
                title-th="รายการ Cluster ที่ผู้ใช้เข้าถึงได้"
                desc-en="Returns only clusters the current user has permission to access."
                desc-th="ส่งคืนเฉพาะ Cluster ที่ผู้ใช้มีสิทธิ์เข้าถึง"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ]"
                :sample-response='json_encode(["data" => [["id" => 1, "name" => "Pattaya", "slug" => "pattaya", "country" => ["name" => "Thailand"]]]], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="GET"
                path="/api/clusters/{clusterId}"
                auth="true"
                title-en="Cluster Detail with Apps"
                title-th="รายละเอียด Cluster พร้อมแอปทั้งหมด"
                desc-en="Returns cluster info with all accessible applications and their modules."
                desc-th="ส่งคืนรายละเอียด Cluster พร้อมแอปและโมดูลที่เข้าถึงได้"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                    ['name' => 'X-Cluster-Id', 'value' => '{clusterId}', 'required' => true],
                ]"
                :params="[
                    ['name' => 'clusterId', 'type' => 'integer', 'required' => true, 'desc_en' => 'Cluster ID', 'desc_th' => 'ID ของ Cluster'],
                ]"
                :sample-response='json_encode(["data" => ["cluster" => ["id" => 1, "name" => "Pattaya", "slug" => "pattaya"], "applications" => [["id" => 1, "name" => "App Together", "code" => "APP_TOGETHER", "type" => "mobile", "modules" => [["id" => 1, "name" => "Explore", "code" => "EXPLORE"]]]]]], JSON_PRETTY_PRINT)'
            />
        </div>

        {{-- Super App Menu --}}
        <div id="menu" class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Super App Menu</h2>
            <p class="text-sm text-gray-500 mb-4">เมนู Dynamic สำหรับ Global Header — กรองตามสิทธิ์ผู้ใช้อัตโนมัติ</p>

            <x-admin.api-endpoint
                method="GET"
                path="/api/menu"
                auth="true"
                title-en="Get Dynamic Menu"
                title-th="ดึงเมนูนำทางแบบ Dynamic"
                desc-en="Returns header menu items filtered by user permissions, visibility rules, and cluster context."
                desc-th="ส่งคืนเมนูที่กรองตามสิทธิ์ผู้ใช้, กฎ visibility, และ cluster context"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                    ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
                ]"
                :sample-response='json_encode(["data" => ["menu" => [["id" => 1, "label" => "Home", "icon" => "home", "url" => "/"], ["id" => 2, "label" => "Explore", "icon" => "compass", "url" => "/explore"]], "cluster" => ["id" => 1, "name" => "Pattaya"]]], JSON_PRETTY_PRINT)'
            />
        </div>

        {{-- Admin APIs --}}
        <div id="admin" class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Admin APIs</h2>
            <p class="text-sm text-gray-500 mb-4">API สำหรับจัดการระบบ — Dashboard, Permissions, User & Group Management</p>

            <x-admin.api-endpoint
                method="GET"
                path="/admin/api/dashboard"
                auth="true"
                title-en="Admin Dashboard Overview"
                title-th="ภาพรวม Dashboard"
                desc-en="Returns statistics based on admin level (Global/Country/Cluster). Global admins see all data."
                desc-th="ส่งคืนสถิติตามระดับสิทธิ์ — Global Admin เห็นข้อมูลทั้งหมด"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                    ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
                ]"
                :sample-response='json_encode(["data" => ["admin_level" => "global", "countries" => 2, "clusters" => 3, "total_users" => 150, "applications" => 10, "api_providers" => 8]], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="POST"
                path="/admin/api/permissions/user-access"
                auth="true"
                title-en="Set User App & Module Access"
                title-th="กำหนดสิทธิ์แอป/โมดูลสำหรับผู้ใช้"
                desc-en="Sets which apps and modules a user can access within a cluster. Most specific permission level."
                desc-th="กำหนดว่าผู้ใช้เข้าถึงแอปและโมดูลใดภายใน Cluster — สิทธิ์ระดับเฉพาะเจาะจงที่สุด"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                    ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
                ]"
                :params="[
                    ['name' => 'user_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Target user ID', 'desc_th' => 'ID ผู้ใช้เป้าหมาย'],
                    ['name' => 'cluster_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Cluster ID', 'desc_th' => 'Cluster ID'],
                    ['name' => 'access', 'type' => 'array', 'required' => true, 'desc_en' => 'Array of {application_id, module_ids[]}', 'desc_th' => 'อาร์เรย์คู่ {application_id, module_ids[]}'],
                ]"
                :sample-body='json_encode(["user_id" => 5, "cluster_id" => 1, "access" => [["application_id" => 1, "module_ids" => [1, 2, 4]]]], JSON_PRETTY_PRINT)'
                :sample-response='json_encode(["message" => "Access updated successfully"], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="POST"
                path="/admin/api/permissions/group-access"
                auth="true"
                title-en="Set Group App & Module Access"
                title-th="กำหนดสิทธิ์แอป/โมดูลสำหรับกลุ่ม"
                desc-en="Sets app/module access for all users in a group. Members inherit unless overridden."
                desc-th="กำหนดสิทธิ์สำหรับสมาชิกทุกคนในกลุ่ม — ถูก override ได้ด้วยสิทธิ์ระดับผู้ใช้"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                    ['name' => 'X-Cluster-Id', 'value' => '1', 'required' => true],
                ]"
                :params="[
                    ['name' => 'group_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Target group ID', 'desc_th' => 'ID กลุ่ม'],
                    ['name' => 'cluster_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Cluster ID', 'desc_th' => 'Cluster ID'],
                    ['name' => 'access', 'type' => 'array', 'required' => true, 'desc_en' => 'Array of {application_id, module_ids[]}', 'desc_th' => 'อาร์เรย์คู่ {application_id, module_ids[]}'],
                ]"
                :sample-body='json_encode(["group_id" => 3, "cluster_id" => 1, "access" => [["application_id" => 1, "module_ids" => [1, 2, 3, 4, 5, 6, 7, 8]]]], JSON_PRETTY_PRINT)'
                :sample-response='json_encode(["message" => "Group access updated successfully"], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="GET"
                path="/admin/api/permissions/user-access-map/{userId}"
                auth="true"
                title-en="Get User Access Map"
                title-th="ดึงแผนที่สิทธิ์การเข้าถึง"
                desc-en="Returns complete access map for a user — which apps/modules they can access in each cluster."
                desc-th="ส่งคืนแผนที่สิทธิ์ทั้งหมด — แอปและโมดูลที่เข้าถึงได้ในแต่ละ Cluster"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ]"
                :params="[
                    ['name' => 'userId', 'type' => 'integer', 'required' => true, 'desc_en' => 'Target user ID (path)', 'desc_th' => 'ID ผู้ใช้ (path)'],
                ]"
                :sample-response='json_encode(["data" => ["1" => ["1" => [1, 2, 3, 4, 5], "3" => [1, 5, 8]]]], JSON_PRETTY_PRINT)'
            />

            <x-admin.api-endpoint
                method="POST"
                path="/admin/api/permissions/assign-role"
                auth="true"
                title-en="Assign Role to User"
                title-th="กำหนดบทบาทให้ผู้ใช้"
                desc-en="Assigns a role with optional country/cluster scope. Available roles: Global Admin, Country Admin, Cluster Admin, App Admin, Operator, Merchant, Tourist."
                desc-th="กำหนดบทบาทพร้อมขอบเขต — Global Admin, Country Admin, Cluster Admin, App Admin, Operator, Merchant, Tourist"
                :headers="[
                    ['name' => 'Authorization', 'value' => 'Bearer {token}', 'required' => true],
                ]"
                :params="[
                    ['name' => 'user_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Target user ID', 'desc_th' => 'ID ผู้ใช้'],
                    ['name' => 'role_id', 'type' => 'integer', 'required' => true, 'desc_en' => 'Role ID', 'desc_th' => 'ID บทบาท'],
                    ['name' => 'country_id', 'type' => 'integer', 'required' => false, 'desc_en' => 'Country scope (for country roles)', 'desc_th' => 'ขอบเขตประเทศ'],
                    ['name' => 'cluster_id', 'type' => 'integer', 'required' => false, 'desc_en' => 'Cluster scope (for cluster roles)', 'desc_th' => 'ขอบเขต Cluster'],
                ]"
                :sample-body='json_encode(["user_id" => 5, "role_id" => 3, "cluster_id" => 1], JSON_PRETTY_PRINT)'
                :sample-response='json_encode(["message" => "Role assigned successfully"], JSON_PRETTY_PRINT)'
            />
        </div>

        {{-- Third-Party Integrations --}}
        <div id="integrations" class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-1">Third-Party Integrations</h2>
            <p class="text-sm text-gray-500 mb-4">API ภายนอกที่เชื่อมต่อกับระบบ — พร้อมขยายเพิ่มเติม</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                            <x-icon name="credit-card" class="w-5 h-5 text-green-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Payment Gateway</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Stripe, PromptPay, VNPay, Local Gateways</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                            <x-icon name="chat-bubble-left" class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">SMS Service</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Twilio, Local SMS providers</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                            <x-icon name="cpu-chip" class="w-5 h-5 text-purple-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">AI Services</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">AI Agent, Call Center, Chatbot, Translation, TTS</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-orange-50 flex items-center justify-center">
                            <x-icon name="star" class="w-5 h-5 text-orange-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Cloud Point API</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Earn, Redeem, Transfer, Exchange points</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center">
                            <x-icon name="arrow-path" class="w-5 h-5 text-teal-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Data Exchange</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Import, Export, Sync between systems</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                            <x-icon name="headphones" class="w-5 h-5 text-red-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">HelpDesk API</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Ticket Management, SLA Tracking, Escalation</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                            <x-icon name="language" class="w-5 h-5 text-indigo-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Translation API</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">TH, EN, ZH, JA, KO, RU — auto-translate content</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-pink-50 flex items-center justify-center">
                            <x-icon name="speaker-wave" class="w-5 h-5 text-pink-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Text-to-Speech</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Voice synthesis for multiple languages</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center">
                            <x-icon name="shield" class="w-5 h-5 text-gray-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Authorization API</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">OAuth2, JWT, RBAC — cluster-aware auth</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                            <x-icon name="currency-dollar" class="w-5 h-5 text-amber-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Currency Exchange API</h4>
                            <x-ui.badge color="green">Ready</x-ui.badge>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">Real-time exchange rates, multi-currency conversion (THB, USD, EUR, JPY, CNY, KRW, VND), transaction history</p>
                </div>
            </div>
        </div>

        {{-- Tech Stack --}}
        <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-base font-bold text-gray-900 mb-3">Technology Stack</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div><span class="text-gray-500">Backend:</span> <span class="font-medium">Laravel 11+ (PHP 8.4)</span></div>
                <div><span class="text-gray-500">Frontend:</span> <span class="font-medium">Blade + Tailwind</span></div>
                <div><span class="text-gray-500">Auth:</span> <span class="font-medium">Sanctum + SSO</span></div>
                <div><span class="text-gray-500">Database:</span> <span class="font-medium">MySQL / PostgreSQL</span></div>
                <div><span class="text-gray-500">Cache:</span> <span class="font-medium">Redis</span></div>
                <div><span class="text-gray-500">Queue:</span> <span class="font-medium">Redis / RabbitMQ</span></div>
                <div><span class="text-gray-500">Search:</span> <span class="font-medium">Meilisearch</span></div>
                <div><span class="text-gray-500">Container:</span> <span class="font-medium">Docker + K8s</span></div>
            </div>
        </div>
    </div>
@endsection
