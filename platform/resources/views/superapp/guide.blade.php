@extends('layouts.superapp')

@section('title', 'User Guide - Thailand Together')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Hero --}}
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold">Thailand Together — User Guide</h1>
            <p class="mt-2 text-orange-100 text-lg">คู่มือการใช้งาน Platform ฉบับสมบูรณ์</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="#overview" class="text-xs px-3 py-1 bg-white/20 rounded-full hover:bg-white/30">1. Overview</a>
                <a href="#getting-started" class="text-xs px-3 py-1 bg-white/20 rounded-full hover:bg-white/30">2. Getting Started</a>
                <a href="#superapp" class="text-xs px-3 py-1 bg-white/20 rounded-full hover:bg-white/30">3. Super App</a>
                <a href="#admin" class="text-xs px-3 py-1 bg-white/20 rounded-full hover:bg-white/30">4. Admin Panel</a>
                <a href="#permissions" class="text-xs px-3 py-1 bg-white/20 rounded-full hover:bg-white/30">5. Permissions</a>
                <a href="#apps" class="text-xs px-3 py-1 bg-white/20 rounded-full hover:bg-white/30">6. Applications</a>
                <a href="#api" class="text-xs px-3 py-1 bg-white/20 rounded-full hover:bg-white/30">7. API Usage</a>
                <a href="#user-types" class="text-xs px-3 py-1 bg-white/20 rounded-full hover:bg-white/30">8. User Types</a>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 1: OVERVIEW --}}
        {{-- ============================================================ --}}
        <div id="overview" class="mb-12">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">1</span>
                <h2 class="text-xl font-bold text-gray-900">Platform Overview / ภาพรวมระบบ</h2>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-2">Thailand Together คืออะไร?</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Thailand Together เป็น <strong>Global Tourism Super App Platform</strong> ที่รวมบริการทั้งหมดสำหรับนักท่องเที่ยวไว้ในที่เดียว
                    — จองโรงแรม, ทัวร์, ร้านอาหาร, ซื้อของ, สะสมแต้ม, แชท, แผนที่ และอีกมากมาย
                </p>
                <p class="text-sm text-gray-600 mb-4">
                    ระบบออกแบบเป็น <strong>Multi-Country, Multi-Cluster</strong> architecture สามารถขยายไปยังเมืองท่องเที่ยวทั่วโลก
                    โดยเริ่มต้น Phase 1 ที่ <strong>Pattaya, Thailand</strong>
                </p>

                {{-- 5-Level Hierarchy Visual --}}
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">ลำดับชั้นของระบบ (5 Levels)</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="w-16 text-xs font-bold text-white bg-gray-800 rounded px-2 py-0.5 text-center">Level 0</span>
                            <span class="font-semibold">Global</span>
                            <span class="text-gray-400">—</span>
                            <span class="text-gray-500">Thailand Together Platform (ศูนย์กลาง)</span>
                        </div>
                        <div class="flex items-center gap-3 ml-6">
                            <span class="w-16 text-xs font-bold text-white bg-blue-600 rounded px-2 py-0.5 text-center">Level 1</span>
                            <span class="font-semibold">Country</span>
                            <span class="text-gray-400">—</span>
                            <span class="text-gray-500">Thailand, Vietnam, ...</span>
                        </div>
                        <div class="flex items-center gap-3 ml-12">
                            <span class="w-16 text-xs font-bold text-white bg-green-600 rounded px-2 py-0.5 text-center">Level 2</span>
                            <span class="font-semibold">Cluster</span>
                            <span class="text-gray-400">—</span>
                            <span class="text-gray-500">Pattaya, Chiang Mai, Danang, ...</span>
                        </div>
                        <div class="flex items-center gap-3 ml-18" style="margin-left:4.5rem">
                            <span class="w-16 text-xs font-bold text-white bg-orange-600 rounded px-2 py-0.5 text-center">Level 3</span>
                            <span class="font-semibold">Application</span>
                            <span class="text-gray-400">—</span>
                            <span class="text-gray-500">App Together, Hotel Mgmt, Tour Booking, ...</span>
                        </div>
                        <div class="flex items-center gap-3 ml-24" style="margin-left:6rem">
                            <span class="w-16 text-xs font-bold text-white bg-purple-600 rounded px-2 py-0.5 text-center">Level 4</span>
                            <span class="font-semibold">Module</span>
                            <span class="text-gray-400">—</span>
                            <span class="text-gray-500">Booking, Map, Chat, Rewards, ...</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Three Pillars --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-3">Three Pillars / 3 เสาหลัก</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <h4 class="font-bold text-sm text-orange-800 mb-1">Pillar 1: App Together</h4>
                        <p class="text-xs text-orange-700">Mobile Super App สำหรับนักท่องเที่ยว — สำรวจ จอง นำทาง สะสมแต้ม Social</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-bold text-sm text-blue-800 mb-1">Pillar 2: Supporting Systems</h4>
                        <p class="text-xs text-blue-700">ระบบหลังบ้าน Laravel — Admin, Hotel, Tour, Marketplace, CRM, HelpDesk</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                        <h4 class="font-bold text-sm text-purple-800 mb-1">Pillar 3: Third-Party APIs</h4>
                        <p class="text-xs text-purple-700">Payment Gateway, SMS, AI Services, Translation, TTS, Cloud Points</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 2: GETTING STARTED --}}
        {{-- ============================================================ --}}
        <div id="getting-started" class="mb-12">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">2</span>
                <h2 class="text-xl font-bold text-gray-900">Getting Started / เริ่มต้นใช้งาน</h2>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-4">วิธีเข้าใช้งานครั้งแรก (Step-by-Step)</h3>

                {{-- Step 1 --}}
                <div class="flex gap-4 mb-6">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">1</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">เข้าหน้าแรก (Landing Page)</h4>
                        <p class="text-sm text-gray-600 mt-1">เปิดเว็บ <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">platform.pattayatogether.com</code></p>
                        <p class="text-sm text-gray-500 mt-1">หน้าแรกจะแสดง <strong>รายการประเทศ</strong> ที่เปิดให้บริการ พร้อม <strong>Cluster (ปลายทาง)</strong> ภายในแต่ละประเทศ</p>
                        <div class="mt-2 bg-gray-50 rounded-lg p-3 text-xs text-gray-600">
                            <strong>ตัวอย่าง:</strong> Thailand > Pattaya (Phase 1) — คลิกที่การ์ด "Pattaya" เพื่อเข้าสู่ Cluster
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="flex gap-4 mb-6">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">2</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">เข้าสู่ระบบ (Login)</h4>
                        <p class="text-sm text-gray-600 mt-1">หากยังไม่ได้ login ระบบจะ redirect ไปหน้า Login</p>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <h5 class="text-xs font-bold text-blue-800 mb-1">Email/Password Login</h5>
                                <p class="text-xs text-blue-700">กรอก email และ password ที่ลงทะเบียนไว้</p>
                                <div class="mt-2 bg-white rounded px-2 py-1 text-xs font-mono text-gray-600">
                                    admin@thailandtogether.com<br>password
                                </div>
                            </div>
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <h5 class="text-xs font-bold text-green-800 mb-1">SSO / Social Login</h5>
                                <p class="text-xs text-green-700">รองรับ 4 providers:</p>
                                <ul class="text-xs text-green-700 mt-1 space-y-0.5">
                                    <li>Google</li>
                                    <li>Facebook</li>
                                    <li>LINE</li>
                                    <li>Apple</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="flex gap-4 mb-6">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">3</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">เลือก Cluster (ปลายทาง)</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            หลัง login จะเห็น Cluster ที่เข้าถึงได้ — คลิกเลือก Cluster เช่น <strong>"Pattaya"</strong>
                        </p>
                        <p class="text-sm text-gray-500 mt-1">Cluster คือ "เมือง" หรือ "ปลายทาง" ที่มีบริการเฉพาะของตัวเอง</p>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="flex gap-4 mb-6">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">4</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">ดูแอปที่มีใน Cluster</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            หน้า Cluster Home จะแสดง <strong>Application Cards</strong> ทั้งหมดที่เปิดใช้งานใน Cluster นั้น
                        </p>
                        <p class="text-sm text-gray-500 mt-1">แต่ละ Card แสดง: ชื่อแอป, ประเภท, จำนวน Modules, คำอธิบาย</p>
                    </div>
                </div>

                {{-- Step 5 --}}
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">5</div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900">เข้าดู Application & Modules</h4>
                        <p class="text-sm text-gray-600 mt-1">
                            คลิกที่ App Card เพื่อดูรายละเอียด — จะเห็น <strong>Module Cards</strong> ทั้งหมดภายในแอปนั้น
                        </p>
                        <p class="text-sm text-gray-500 mt-1">Module คือ ฟีเจอร์ย่อยภายในแอป เช่น Booking, Map, Chat, Rewards</p>
                    </div>
                </div>
            </div>

            {{-- Navigation Flow Diagram --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-3">Navigation Flow / การนำทาง</h3>
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="bg-gray-100 px-3 py-1.5 rounded-lg font-medium">Landing</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-gray-400" />
                    <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-lg font-medium">Login</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-gray-400" />
                    <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-lg font-medium">Cluster Home</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-gray-400" />
                    <span class="bg-orange-100 text-orange-800 px-3 py-1.5 rounded-lg font-medium">App Detail</span>
                    <x-icon name="arrow-right" class="w-4 h-4 text-gray-400" />
                    <span class="bg-purple-100 text-purple-800 px-3 py-1.5 rounded-lg font-medium">Modules</span>
                </div>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-2 text-xs text-center">
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="font-mono text-gray-500">/</p>
                        <p class="text-gray-600 mt-1">เลือกประเทศ<br>& Cluster</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="font-mono text-gray-500">/login</p>
                        <p class="text-gray-600 mt-1">Email/Password<br>หรือ SSO</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="font-mono text-gray-500">/cluster/pattaya</p>
                        <p class="text-gray-600 mt-1">ดู 15 แอป<br>ใน Pattaya</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="font-mono text-gray-500">/cluster/pattaya/app/1</p>
                        <p class="text-gray-600 mt-1">ดู Modules<br>ภายในแอป</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="font-mono text-gray-500">Module Cards</p>
                        <p class="text-gray-600 mt-1">ฟีเจอร์ย่อย<br>แต่ละตัว</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 3: SUPER APP --}}
        {{-- ============================================================ --}}
        <div id="superapp" class="mb-12">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">3</span>
                <h2 class="text-xl font-bold text-gray-900">Super App Shell / หน้าตาระบบหลัก</h2>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-3">Global Header (แถบนำทาง)</h3>
                <p class="text-sm text-gray-600 mb-3">Header จะแสดงเหมือนกันทุกหน้า ประกอบด้วย:</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-orange-500 rounded"></div>
                            <span class="font-bold text-sm">Thailand Together</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded">Pattaya</span>
                            <span class="bg-gray-200 px-2 py-0.5 rounded">User Menu</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                        <div class="text-center p-2 bg-white rounded border">
                            <p class="font-semibold text-gray-800">Logo</p>
                            <p class="text-gray-500">กลับหน้า Landing</p>
                        </div>
                        <div class="text-center p-2 bg-white rounded border">
                            <p class="font-semibold text-gray-800">Cluster Switcher</p>
                            <p class="text-gray-500">สลับ Cluster</p>
                        </div>
                        <div class="text-center p-2 bg-white rounded border">
                            <p class="font-semibold text-gray-800">User Menu</p>
                            <p class="text-gray-500">Admin, API, Guide, Logout</p>
                        </div>
                        <div class="text-center p-2 bg-white rounded border">
                            <p class="font-semibold text-gray-800">Mobile Menu</p>
                            <p class="text-gray-500">เมนู Hamburger</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-3">Cluster Home Page / หน้าหลัก Cluster</h3>
                <p class="text-sm text-gray-600 mb-3">
                    เมื่อเข้าสู่ Cluster (เช่น Pattaya) จะเห็น Application Cards ทั้งหมดที่เปิดให้บริการ:
                </p>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-2 text-xs">
                    @php
                        $demoApps = [
                            ['name' => 'App Together', 'type' => 'mobile', 'color' => 'orange'],
                            ['name' => 'Hotel Management', 'type' => 'web', 'color' => 'blue'],
                            ['name' => 'Tour Booking', 'type' => 'web', 'color' => 'green'],
                            ['name' => 'Marketplace', 'type' => 'web', 'color' => 'purple'],
                            ['name' => 'Restaurant Booking', 'type' => 'web', 'color' => 'red'],
                            ['name' => 'Transport', 'type' => 'web', 'color' => 'teal'],
                            ['name' => 'Events & Shows', 'type' => 'web', 'color' => 'pink'],
                            ['name' => 'CRM', 'type' => 'web', 'color' => 'indigo'],
                            ['name' => 'HelpDesk', 'type' => 'web', 'color' => 'amber'],
                            ['name' => 'AI Services', 'type' => 'web', 'color' => 'violet'],
                            ['name' => 'City Dashboard', 'type' => 'web', 'color' => 'sky'],
                            ['name' => 'Gov ERP (GFMIS)', 'type' => 'web', 'color' => 'teal'],
                            ['name' => 'Event & MICE', 'type' => 'hybrid', 'color' => 'pink'],
                            ['name' => 'Project Management', 'type' => 'web', 'color' => 'violet'],
                            ['name' => 'Data Exchange', 'type' => 'web', 'color' => 'emerald'],
                        ];
                    @endphp
                    @foreach($demoApps as $dApp)
                        <div class="bg-{{ $dApp['color'] }}-50 rounded-lg p-2 text-center border border-{{ $dApp['color'] }}-100">
                            <p class="font-semibold text-gray-800">{{ $dApp['name'] }}</p>
                            <span class="text-gray-500">{{ $dApp['type'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-3">App Detail Page / หน้ารายละเอียดแอป</h3>
                <p class="text-sm text-gray-600 mb-3">คลิกที่ App Card จะเห็น Module Cards ภายใน เช่น:</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="font-semibold text-sm mb-2">App Together — 8 Modules:</p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                        @php
                            $demoModules = ['Explore & Discover', 'Booking Hub', 'Interactive Map', 'Reward Points', 'Live Chat', 'Digital Wallet', 'Social Feed', 'Notifications'];
                        @endphp
                        @foreach($demoModules as $mod)
                            <div class="bg-white rounded border border-gray-200 p-2 text-center">
                                <p class="font-medium text-gray-800">{{ $mod }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 4: ADMIN PANEL --}}
        {{-- ============================================================ --}}
        <div id="admin" class="mb-12">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">4</span>
                <h2 class="text-xl font-bold text-gray-900">Admin Panel / ระบบจัดการหลังบ้าน</h2>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-2">วิธีเข้าใช้ Admin Panel</h3>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-amber-800">
                        <strong>URL:</strong> <code class="bg-amber-100 px-1 rounded">/admin/login</code><br>
                        <strong>Demo Account:</strong> <code class="bg-amber-100 px-1 rounded">admin@thailandtogether.com</code> / <code class="bg-amber-100 px-1 rounded">password</code>
                    </p>
                </div>
                <p class="text-sm text-gray-600 mb-3">หรือเข้าผ่าน <strong>User Menu > Admin Panel</strong> ในหน้า Super App</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-3">Admin Panel Features</h3>
                <div class="space-y-4">
                    {{-- Dashboard --}}
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                            <x-icon name="chart-bar" class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Dashboard</h4>
                            <p class="text-xs text-gray-500">
                                ภาพรวมสถิติ — จำนวน Applications, Active Users, Clusters, Countries, API Providers<br>
                                Quick Actions — ลัดไป Manage Applications, Permission Management, User Management<br>
                                System Info — แสดง Admin Level (Global/Country/Cluster) และเวอร์ชัน Platform
                            </p>
                        </div>
                    </div>

                    {{-- Applications --}}
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                            <x-icon name="cube" class="w-5 h-5 text-orange-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Applications Management</h4>
                            <p class="text-xs text-gray-500">
                                ดูรายการแอปทั้งหมด (15 apps) พร้อมข้อมูล: ชื่อ, รหัส, ประเภท (web/mobile/hybrid), จำนวน Modules<br>
                                คลิกเข้าดูรายละเอียดแต่ละ App — เห็น Modules ทั้งหมดพร้อมสถานะ Active/Inactive
                            </p>
                        </div>
                    </div>

                    {{-- API Reference --}}
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                            <x-icon name="code-bracket" class="w-5 h-5 text-green-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">API Reference</h4>
                            <p class="text-xs text-gray-500">
                                เอกสาร API ทั้งหมดในระบบ — Auth, Clusters, Menu, Admin APIs<br>
                                แต่ละ Endpoint มี: Method, URL, Parameters, Headers, Sample Request/Response<br>
                                มี Sandbox (ทดสอบ API จริงจากหน้าเว็บได้เลย) — 2 ภาษา (TH/EN)
                            </p>
                        </div>
                    </div>

                    {{-- Permissions --}}
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                            <x-icon name="shield" class="w-5 h-5 text-purple-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Permissions Hub</h4>
                            <p class="text-xs text-gray-500">
                                ศูนย์กลางจัดการสิทธิ์ — มี 4 หน้าย่อย:<br>
                                <strong>Overview:</strong> ภาพรวม Role-Group-User<br>
                                <strong>Users:</strong> จัดการผู้ใช้ — ดูรายชื่อ, กำหนดสิทธิ์, assign role<br>
                                <strong>Groups:</strong> จัดการกลุ่ม — Operators, Merchants, Tourists, VIP<br>
                                <strong>Roles:</strong> จัดการบทบาท — Global Admin, Country Admin, Cluster Admin, ...
                            </p>
                        </div>
                    </div>

                    {{-- Roadmap --}}
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center">
                            <x-icon name="clipboard-document-list" class="w-5 h-5 text-teal-600" />
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm text-gray-900">Roadmap & Checklist</h4>
                            <p class="text-xs text-gray-500">
                                แผนพัฒนาและ Checklist สิ่งที่ต้องทำต่อ — แบ่งเป็น Phase พร้อม Priority<br>
                                ดูสถานะ: สร้างเสร็จแล้ว / กำลังทำ / ยังไม่ได้ทำ
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Admin Sidebar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-3">Admin Navigation (Sidebar)</h3>
                <div class="bg-gray-50 rounded-lg p-3 max-w-xs">
                    <div class="space-y-1 text-sm">
                        <div class="flex items-center gap-2 px-3 py-2 bg-orange-50 text-orange-700 rounded-lg font-medium">
                            <x-icon name="chart-bar" class="w-4 h-4" /> Dashboard
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 text-gray-600 rounded-lg">
                            <x-icon name="cube" class="w-4 h-4" /> Applications
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 text-gray-600 rounded-lg">
                            <x-icon name="code-bracket" class="w-4 h-4" /> API Reference
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 text-gray-600 rounded-lg">
                            <x-icon name="clipboard-document-list" class="w-4 h-4" /> Roadmap
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 text-gray-600 rounded-lg">
                            <x-icon name="shield" class="w-4 h-4" /> Permissions
                        </div>
                        <div class="ml-6 space-y-0.5 text-xs text-gray-500">
                            <p class="py-1">Overview</p>
                            <p class="py-1">Users</p>
                            <p class="py-1">Groups</p>
                            <p class="py-1">Roles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 5: PERMISSIONS --}}
        {{-- ============================================================ --}}
        <div id="permissions" class="mb-12">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">5</span>
                <h2 class="text-xl font-bold text-gray-900">Permission System / ระบบสิทธิ์การเข้าถึง</h2>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-3">Role Hierarchy / ลำดับบทบาท</h3>
                <p class="text-sm text-gray-600 mb-4">บทบาทจัดลำดับจากมากไปน้อย — ระดับสูงสุดมีสิทธิ์เข้าถึงทุกอย่างในระดับที่ต่ำกว่า</p>
                <div class="space-y-2">
                    <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-red-800">Global Admin</span>
                                <p class="text-xs text-red-600 mt-0.5">เข้าถึง ทุกประเทศ, ทุก Cluster, ทุก App, ทุก Module</p>
                            </div>
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">Scope: Global</span>
                        </div>
                    </div>
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded-r-lg ml-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-blue-800">Country Admin</span>
                                <p class="text-xs text-blue-600 mt-0.5">เข้าถึงทุก Cluster ภายในประเทศที่รับผิดชอบ</p>
                            </div>
                            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">Scope: Country</span>
                        </div>
                    </div>
                    <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-r-lg ml-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-green-800">Cluster Admin</span>
                                <p class="text-xs text-green-600 mt-0.5">เข้าถึงทุก App ภายใน Cluster ที่รับผิดชอบ</p>
                            </div>
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Scope: Cluster</span>
                        </div>
                    </div>
                    <div class="bg-orange-50 border-l-4 border-orange-500 p-3 rounded-r-lg ml-12">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-orange-800">App Admin</span>
                                <p class="text-xs text-orange-600 mt-0.5">เข้าถึงทุก Module ภายใน App ที่รับผิดชอบ</p>
                            </div>
                            <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded">Scope: App</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 border-l-4 border-gray-400 p-3 rounded-r-lg ml-16">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-bold text-gray-800">Operator / Merchant / Tourist</span>
                                <p class="text-xs text-gray-600 mt-0.5">เข้าถึงเฉพาะ Apps & Modules ที่ได้รับกำหนดผ่าน Group หรือ User Access</p>
                            </div>
                            <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded">Scope: Custom</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-3">Group System / ระบบกลุ่ม</h3>
                <p class="text-sm text-gray-600 mb-3">ผู้ใช้ถูกจัดเข้ากลุ่ม — กลุ่มจะกำหนดว่าเข้าถึง App/Module ใดได้บ้าง:</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <h4 class="font-bold text-xs text-blue-800">#Operators</h4>
                        <p class="text-xs text-blue-600 mt-1">พนักงานดูแลระบบ — เข้าถึง Admin, CRM, HelpDesk</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                        <h4 class="font-bold text-xs text-green-800">#Merchants</h4>
                        <p class="text-xs text-green-600 mt-1">ผู้ประกอบการ — เข้าถึง Hotel Mgmt, Tour, Marketplace</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-3 border border-orange-100">
                        <h4 class="font-bold text-xs text-orange-800">#Tourists</h4>
                        <p class="text-xs text-orange-600 mt-1">นักท่องเที่ยว — เข้าถึง App Together ทุก Module</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-3">Permission Resolution (7 Steps)</h3>
                <p class="text-sm text-gray-600 mb-3">เมื่อผู้ใช้พยายามเข้าถึง Module ระบบจะตรวจสอบ 7 ขั้นตอน:</p>
                <div class="space-y-2">
                    @php
                        $steps = [
                            ['step' => 'SSO Auth', 'desc' => 'ตรวจ token — ผู้ใช้ยืนยันตัวตนแล้วหรือไม่'],
                            ['step' => 'Country', 'desc' => 'มีสิทธิ์เข้าประเทศนี้หรือไม่'],
                            ['step' => 'Cluster', 'desc' => 'มีสิทธิ์เข้า Cluster นี้หรือไม่'],
                            ['step' => 'Application', 'desc' => 'มีสิทธิ์ใช้ App นี้หรือไม่'],
                            ['step' => 'Module', 'desc' => 'มีสิทธิ์ใช้ Module นี้หรือไม่'],
                            ['step' => 'Group Override', 'desc' => 'กลุ่มของผู้ใช้มี grant/deny พิเศษหรือไม่'],
                            ['step' => 'User Override', 'desc' => 'ผู้ใช้มีสิทธิ์เฉพาะตัวหรือไม่ (สิทธิ์ที่เฉพาะเจาะจงที่สุดชนะ)'],
                        ];
                    @endphp
                    @foreach($steps as $i => $step)
                        <div class="flex items-start gap-3 bg-gray-50 rounded-lg p-2.5">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs flex items-center justify-center font-bold">{{ $i + 1 }}</span>
                            <div>
                                <span class="text-sm font-semibold text-gray-900">{{ $step['step'] }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $step['desc'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 bg-green-50 border border-green-200 rounded-lg p-3">
                    <p class="text-xs text-green-800"><strong>หลักการสำคัญ:</strong> สิทธิ์ที่เฉพาะเจาะจงที่สุดจะชนะ (Most Specific Rule Wins) — User Override > Group Override > Module > App > Cluster > Country</p>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 6: 10 APPLICATIONS --}}
        {{-- ============================================================ --}}
        <div id="apps" class="mb-12">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">6</span>
                <h2 class="text-xl font-bold text-gray-900">15 Applications / แอปพลิเคชันทั้ง 15 ตัว</h2>
            </div>

            <div class="space-y-3">
                @php
                    $applications = [
                        [
                            'name' => 'App Together',
                            'code' => 'APP_TOGETHER',
                            'type' => 'Mobile Super App',
                            'color' => 'orange',
                            'desc' => 'แอปหลักสำหรับนักท่องเที่ยว — รวมทุกบริการไว้ในที่เดียว',
                            'modules' => ['Explore & Discover', 'Booking Hub', 'Interactive Map', 'Reward Points', 'Live Chat', 'Digital Wallet', 'Social Feed', 'Notifications'],
                        ],
                        [
                            'name' => 'Hotel Management',
                            'code' => 'HOTEL_MGMT',
                            'type' => 'Web Application',
                            'color' => 'blue',
                            'desc' => 'ระบบจัดการโรงแรม — จองห้อง, Check-in/out, ราคา, ห้องว่าง',
                            'modules' => ['Room Booking', 'Availability Calendar', 'Pricing Engine', 'Guest Management', 'Check-in/Check-out', 'Revenue Dashboard'],
                        ],
                        [
                            'name' => 'Tour Booking',
                            'code' => 'TOUR_BOOKING',
                            'type' => 'Web Application',
                            'color' => 'green',
                            'desc' => 'ระบบจองทัวร์ — สร้างทัวร์, จัดตาราง, จอง, รีวิว',
                            'modules' => ['Tour Catalog', 'Schedule Management', 'Online Booking', 'Guide Management', 'Review & Ratings', 'Group Packages'],
                        ],
                        [
                            'name' => 'Marketplace',
                            'code' => 'MARKETPLACE',
                            'type' => 'Web Application',
                            'color' => 'purple',
                            'desc' => 'ตลาดออนไลน์ — ขายสินค้า ของฝาก อาหาร บริการ',
                            'modules' => ['Product Catalog', 'Shopping Cart', 'Order Management', 'Seller Dashboard', 'Reviews & Ratings', 'Promotions'],
                        ],
                        [
                            'name' => 'Restaurant Booking',
                            'code' => 'RESTAURANT',
                            'type' => 'Web Application',
                            'color' => 'red',
                            'desc' => 'จองร้านอาหาร — ค้นหา จอง เมนู รีวิว',
                            'modules' => ['Restaurant Directory', 'Table Reservation', 'Menu Management', 'Delivery Orders', 'Review & Ratings'],
                        ],
                        [
                            'name' => 'Transport & Mobility',
                            'code' => 'TRANSPORT',
                            'type' => 'Web Application',
                            'color' => 'teal',
                            'desc' => 'บริการขนส่ง — เรียกรถ เช่ารถ เส้นทาง',
                            'modules' => ['Ride Hailing', 'Vehicle Rental', 'Route Planner', 'Shuttle Service', 'Fare Calculator'],
                        ],
                        [
                            'name' => 'Events & Shows',
                            'code' => 'EVENTS',
                            'type' => 'Web Application',
                            'color' => 'pink',
                            'desc' => 'อีเวนต์และโชว์ — ปฏิทิน จองตั๋ว โปรโมชัน',
                            'modules' => ['Event Calendar', 'Ticket Booking', 'Venue Management', 'Promotions', 'Live Streaming'],
                        ],
                        [
                            'name' => 'CRM',
                            'code' => 'CRM',
                            'type' => 'Internal System',
                            'color' => 'indigo',
                            'desc' => 'ระบบบริหารลูกค้าสัมพันธ์ — ข้อมูลลูกค้า แคมเปญ วิเคราะห์',
                            'modules' => ['Contact Management', 'Campaign Manager', 'Analytics Dashboard', 'Segmentation', 'Email/SMS Marketing'],
                        ],
                        [
                            'name' => 'HelpDesk',
                            'code' => 'HELPDESK',
                            'type' => 'Internal System',
                            'color' => 'amber',
                            'desc' => 'ระบบ Ticket Support — แจ้งปัญหา ติดตาม SLA',
                            'modules' => ['Ticket System', 'SLA Tracking', 'Knowledge Base', 'Escalation Rules', 'Satisfaction Survey'],
                        ],
                        [
                            'name' => 'AI Services Hub',
                            'code' => 'AI_SERVICES',
                            'type' => 'Hybrid',
                            'color' => 'violet',
                            'desc' => 'ศูนย์บริการ AI — Chatbot แปลภาษา TTS Call Center',
                            'modules' => ['AI Chatbot', 'Translation Engine', 'Text-to-Speech', 'AI Call Center', 'Recommendation Engine'],
                        ],
                        [
                            'name' => 'City Dashboard',
                            'code' => 'CITY_DASHBOARD',
                            'type' => 'Web Application',
                            'color' => 'sky',
                            'desc' => 'แดชบอร์ดเมืองอัจฉริยะ — สถิตินักท่องเที่ยว การจราจร รายได้ ความปลอดภัย สิ่งแวดล้อม',
                            'modules' => ['Tourism Statistics', 'Traffic & Mobility', 'Revenue & Tax Monitor', 'Public Safety Monitor', 'Environmental Monitor', 'Business Intelligence', 'Citizen Feedback'],
                        ],
                        [
                            'name' => 'Government ERP (NEW GFMIS)',
                            'code' => 'GOV_ERP',
                            'type' => 'Web Application',
                            'color' => 'teal',
                            'desc' => 'ระบบ ERP ภาครัฐ — งบประมาณ จัดซื้อจัดจ้าง บัญชี สินทรัพย์ บุคลากร เอกสาร ตรวจสอบ',
                            'modules' => ['Budget Management', 'Procurement & e-GP', 'Financial Reporting', 'Asset Management', 'HR & Payroll', 'Document Management', 'Audit & Compliance'],
                        ],
                        [
                            'name' => 'Event & MICE',
                            'code' => 'EVENT_MICE',
                            'type' => 'Hybrid',
                            'color' => 'pink',
                            'desc' => 'จัดการอีเวนต์และ MICE — Meetings, Incentives, Conferences, Exhibitions ครบวงจร',
                            'modules' => ['Event Management', 'Venue Booking', 'MICE Planner', 'Ticket & Registration', 'Exhibitor Portal', 'Attendee Engagement', 'Event Analytics'],
                        ],
                        [
                            'name' => 'Project Management',
                            'code' => 'PROJECT_MGMT',
                            'type' => 'Web Application',
                            'color' => 'violet',
                            'desc' => 'ระบบบริหารโครงการ — วางแผน ติดตาม Gantt chart จัดสรรทรัพยากร งบประมาณ บริหารความเสี่ยง',
                            'modules' => ['Project Dashboard', 'Task & Gantt', 'Resource Management', 'Budget & Cost Tracking', 'Document & Files', 'Risk Management', 'Reporting & Analytics'],
                        ],
                        [
                            'name' => 'Data Exchange',
                            'code' => 'DATA_EXCHANGE',
                            'type' => 'Web Application',
                            'color' => 'emerald',
                            'desc' => 'ศูนย์แลกเปลี่ยนข้อมูลกลาง — Data Catalog, คุณภาพข้อมูล, ความปลอดภัย, High Value Dataset, Sandbox, ติดตามการใช้งาน',
                            'modules' => ['Data Index', 'Data Catalog', 'Data Quality', 'Data Security', 'High Value Dataset', 'Data Sandbox', 'Data Request', 'Tracking & Monitoring'],
                        ],
                    ];
                @endphp

                @foreach($applications as $app)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center gap-4 p-4 text-left hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-lg bg-{{ $app['color'] }}-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-{{ $app['color'] }}-600 font-bold text-sm">{{ substr($app['code'], 0, 2) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-gray-900">{{ $app['name'] }}</h4>
                                    <span class="text-xs bg-{{ $app['color'] }}-50 text-{{ $app['color'] }}-700 px-2 py-0.5 rounded">{{ $app['type'] }}</span>
                                </div>
                                <p class="text-xs text-gray-500 truncate">{{ $app['desc'] }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ count($app['modules']) }} modules</span>
                            <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 transition-transform" ::class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" x-collapse x-cloak class="border-t border-gray-100 px-4 py-3">
                            <p class="text-xs font-semibold text-gray-500 mb-2">Modules:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($app['modules'] as $module)
                                    <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg">{{ $module }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 7: API USAGE --}}
        {{-- ============================================================ --}}
        <div id="api" class="mb-12">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">7</span>
                <h2 class="text-xl font-bold text-gray-900">API Usage / การใช้งาน API</h2>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-3">Quick Start — เริ่มต้นเรียก API</h3>

                {{-- Step 1: Login --}}
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Step 1: Login เพื่อรับ Token</h4>
                    <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-xs font-mono overflow-x-auto">POST /api/auth/login
Content-Type: application/json

{
    "email": "admin@thailandtogether.com",
    "password": "password"
}

// Response:
{
    "token": "1|abc123xyz...",
    "token_type": "Bearer"
}</pre>
                </div>

                {{-- Step 2: Use Token --}}
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Step 2: ใช้ Token เรียก API</h4>
                    <pre class="bg-gray-900 text-blue-400 rounded-lg p-4 text-xs font-mono overflow-x-auto">GET /api/clusters/1
Authorization: Bearer 1|abc123xyz...
X-Cluster-Id: 1
Accept: application/json

// Response:
{
    "data": {
        "cluster": { "id": 1, "name": "Pattaya" },
        "applications": [...]
    }
}</pre>
                </div>

                {{-- Step 3: Cluster Header --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-800 mb-2">Step 3: ส่ง X-Cluster-Id ทุก request</h4>
                    <p class="text-xs text-gray-600 mb-2">API ส่วนใหญ่ต้องการ <code class="bg-gray-100 px-1 rounded">X-Cluster-Id</code> header เพื่อระบุว่าทำงานกับ Cluster ไหน</p>
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800">
                        <strong>สำคัญ:</strong> Headers ที่ต้องส่งเสมอ:
                        <ul class="mt-1 space-y-0.5 list-disc list-inside">
                            <li><code>Authorization: Bearer {token}</code> — สำหรับ protected endpoints</li>
                            <li><code>X-Cluster-Id: {id}</code> — สำหรับ cluster-aware endpoints</li>
                            <li><code>Accept: application/json</code> — เพื่อรับ JSON response</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-900 mb-3">Available Endpoints Summary</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500">Method</th>
                                <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500">Endpoint</th>
                                <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500">Auth</th>
                                <th class="text-left px-3 py-2 text-xs font-semibold text-gray-500">Description</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @php
                                $endpoints = [
                                    ['POST', '/api/auth/login', false, 'Login ด้วย Email/Password'],
                                    ['POST', '/api/auth/sso', false, 'Login ด้วย SSO (Google, Facebook, LINE, Apple)'],
                                    ['GET', '/api/auth/session', true, 'ดึง Session & Access Map ปัจจุบัน'],
                                    ['POST', '/api/auth/logout', true, 'Logout / เพิกถอน Token'],
                                    ['GET', '/api/countries', false, 'รายการประเทศ & Cluster ทั้งหมด'],
                                    ['GET', '/api/clusters/accessible', true, 'Cluster ที่ผู้ใช้เข้าถึงได้'],
                                    ['GET', '/api/clusters/{id}', true, 'รายละเอียด Cluster & Apps'],
                                    ['GET', '/api/menu', true, 'Dynamic Header Menu'],
                                    ['GET', '/admin/api/dashboard', true, 'Admin Dashboard Stats'],
                                    ['POST', '/admin/api/permissions/user-access', true, 'กำหนดสิทธิ์ App/Module สำหรับผู้ใช้'],
                                    ['POST', '/admin/api/permissions/group-access', true, 'กำหนดสิทธิ์ App/Module สำหรับกลุ่ม'],
                                    ['GET', '/admin/api/permissions/user-access-map/{id}', true, 'แผนที่สิทธิ์ของผู้ใช้'],
                                    ['POST', '/admin/api/permissions/assign-role', true, 'กำหนดบทบาทให้ผู้ใช้'],
                                ];
                            @endphp
                            @foreach($endpoints as $ep)
                                <tr class="border-b border-gray-50">
                                    <td class="px-3 py-2">
                                        <span @class([
                                            'px-1.5 py-0.5 rounded font-bold',
                                            'bg-green-100 text-green-700' => $ep[0] === 'GET',
                                            'bg-blue-100 text-blue-700' => $ep[0] === 'POST',
                                        ])>{{ $ep[0] }}</span>
                                    </td>
                                    <td class="px-3 py-2 font-mono text-gray-700">{{ $ep[1] }}</td>
                                    <td class="px-3 py-2">
                                        @if($ep[2])
                                            <span class="text-amber-600">Required</span>
                                        @else
                                            <span class="text-green-600">Public</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-gray-600">{{ $ep[3] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ route('superapp.api-docs') }}" class="text-sm font-medium text-(--color-primary) hover:underline">
                        ดู API Reference ฉบับสมบูรณ์ (พร้อม Sandbox) →
                    </a>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- SECTION 8: USER TYPES --}}
        {{-- ============================================================ --}}
        <div id="user-types" class="mb-12">
            <div class="flex items-center gap-3 mb-4">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 font-bold text-sm">8</span>
                <h2 class="text-xl font-bold text-gray-900">User Types / ประเภทผู้ใช้งาน</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Tourists --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                            <x-icon name="globe" class="w-5 h-5 text-orange-600" />
                        </div>
                        <h3 class="font-bold text-gray-900">Tourists / นักท่องเที่ยว</h3>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">ผู้ใช้หลักของ Platform — ค้นหา จอง ใช้บริการ สะสมแต้ม</p>
                    <div class="text-xs text-gray-500 space-y-1">
                        <p><strong>เข้าถึง:</strong> App Together (ทุก Module)</p>
                        <p><strong>Login:</strong> SSO (Google, Facebook, LINE, Apple)</p>
                        <p><strong>Cross-Cluster:</strong> ใช้แต้มข้าม Cluster ได้</p>
                    </div>
                </div>

                {{-- Merchants --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                            <x-icon name="building-storefront" class="w-5 h-5 text-green-600" />
                        </div>
                        <h3 class="font-bold text-gray-900">Merchants / ผู้ประกอบการ</h3>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">โรงแรม ร้านอาหาร ทัวร์ ร้านค้า — จัดการธุรกิจผ่าน Platform</p>
                    <div class="text-xs text-gray-500 space-y-1">
                        <p><strong>เข้าถึง:</strong> Hotel Mgmt, Tour Booking, Marketplace, Restaurant</p>
                        <p><strong>Login:</strong> Email/Password</p>
                        <p><strong>Dashboard:</strong> ดูยอดจอง ยอดขาย รีวิว</p>
                    </div>
                </div>

                {{-- Operators --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <x-icon name="wrench" class="w-5 h-5 text-blue-600" />
                        </div>
                        <h3 class="font-bold text-gray-900">Operators / ผู้ดูแลระบบ</h3>
                    </div>
                    <p class="text-xs text-gray-600 mb-3">พนักงานที่ดูแลการทำงาน Platform — CRM, HelpDesk, AI</p>
                    <div class="text-xs text-gray-500 space-y-1">
                        <p><strong>เข้าถึง:</strong> CRM, HelpDesk, AI Services + Admin Panel</p>
                        <p><strong>Login:</strong> Email/Password</p>
                        <p><strong>Permissions:</strong> ตามที่ Admin กำหนดผ่าน Group/Role</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="bg-gray-100 rounded-xl p-6">
            <h3 class="font-bold text-gray-900 mb-3">Quick Links / ลิงก์ที่เกี่ยวข้อง</h3>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('superapp.landing') }}" class="bg-white px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-200">Home</a>
                <a href="{{ route('superapp.api-docs') }}" class="bg-white px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-200">API Reference</a>
                <a href="{{ route('admin.login') }}" class="bg-white px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-200">Admin Panel</a>
                <a href="{{ route('login') }}" class="bg-white px-4 py-2 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 border border-gray-200">Login</a>
            </div>
        </div>
    </div>
@endsection
