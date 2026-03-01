@props([
    'method' => 'GET',
    'path' => '/',
    'auth' => 'true',
    'titleEn' => '',
    'titleTh' => '',
    'descEn' => '',
    'descTh' => '',
    'params' => [],
    'headers' => [],
    'sampleBody' => null,
    'sampleResponse' => '{}',
])

@php
    $methodColors = [
        'GET' => 'bg-green-100 text-green-700 border-green-200',
        'POST' => 'bg-blue-100 text-blue-700 border-blue-200',
        'PUT' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
        'PATCH' => 'bg-orange-100 text-orange-700 border-orange-200',
        'DELETE' => 'bg-red-100 text-red-700 border-red-200',
    ];
    $methodColor = $methodColors[$method] ?? $methodColors['GET'];
    $methodBorder = [
        'GET' => 'border-l-green-400',
        'POST' => 'border-l-blue-400',
        'PUT' => 'border-l-yellow-400',
        'DELETE' => 'border-l-red-400',
    ][$method] ?? 'border-l-gray-400';
    $uniqueId = 'api-' . md5($method . $path);
@endphp

<div x-data="{
        expanded: false,
        sandbox: false,
        loading: false,
        responseStatus: null,
        responseBody: '',
        responseTime: null,
        sandboxHeaders: @js(collect($headers)->mapWithKeys(fn($h) => [$h['name'] => $h['value']])->toArray()),
        sandboxBody: {{ $sampleBody ? "'" . addslashes($sampleBody) . "'" : "''" }},
        sandboxUrl: '{{ url($path) }}',
        async sendRequest() {
            this.loading = true;
            this.responseStatus = null;
            this.responseBody = '';
            this.responseTime = null;
            const start = performance.now();
            try {
                const opts = {
                    method: '{{ $method }}',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        ...Object.fromEntries(
                            Object.entries(this.sandboxHeaders).filter(([k, v]) => v && !v.includes('{'))
                        ),
                    },
                };
                if ('{{ $method }}' !== 'GET' && this.sandboxBody) {
                    opts.body = this.sandboxBody;
                }
                const resp = await fetch(this.sandboxUrl, opts);
                this.responseTime = Math.round(performance.now() - start);
                this.responseStatus = resp.status;
                const text = await resp.text();
                try {
                    this.responseBody = JSON.stringify(JSON.parse(text), null, 2);
                } catch {
                    this.responseBody = text;
                }
            } catch (err) {
                this.responseTime = Math.round(performance.now() - start);
                this.responseStatus = 0;
                this.responseBody = 'Network Error: ' + err.message;
            }
            this.loading = false;
        }
     }"
     class="mb-4 bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 {{ $methodBorder }} overflow-hidden">

    {{-- Header (clickable) --}}
    <button @click="expanded = !expanded" class="w-full flex items-center gap-3 px-5 py-4 text-left hover:bg-gray-50 transition-colors">
        <span class="flex-shrink-0 px-2.5 py-1 text-xs font-bold rounded border {{ $methodColor }}">
            {{ $method }}
        </span>
        <code class="text-sm font-mono text-gray-700 flex-1 truncate">{{ $path }}</code>
        @if($auth === 'true')
            <x-icon name="lock-closed" class="w-4 h-4 text-amber-500 flex-shrink-0" title="Authentication required" />
        @else
            <x-icon name="lock-open" class="w-4 h-4 text-green-500 flex-shrink-0" title="Public endpoint" />
        @endif
        <span class="hidden sm:block text-sm text-gray-500 flex-shrink-0 max-w-xs truncate">{{ $titleEn }}</span>
        <x-icon name="chevron-down" class="w-4 h-4 text-gray-400 flex-shrink-0 transition-transform" ::class="expanded ? 'rotate-180' : ''" />
    </button>

    {{-- Expanded content --}}
    <div x-show="expanded" x-collapse x-cloak>
        <div class="border-t border-gray-100 px-5 py-4 space-y-5">

            {{-- Title & description --}}
            <div>
                <h3 class="text-base font-bold text-gray-900">{{ $titleEn }}</h3>
                <p class="text-sm text-gray-400">{{ $titleTh }}</p>
                <p class="mt-2 text-sm text-gray-600">{{ $descEn }}</p>
                <p class="text-sm text-gray-400">{{ $descTh }}</p>
            </div>

            {{-- Auth badge --}}
            <div class="flex items-center gap-2">
                @if($auth === 'true')
                    <x-ui.badge color="orange">
                        <x-icon name="lock-closed" class="w-3 h-3 mr-1" />
                        Auth Required / ต้องยืนยันตัวตน
                    </x-ui.badge>
                @else
                    <x-ui.badge color="green">
                        <x-icon name="lock-open" class="w-3 h-3 mr-1" />
                        Public / เปิดสาธารณะ
                    </x-ui.badge>
                @endif
            </div>

            {{-- Headers --}}
            @if(!empty($headers))
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Headers</h4>
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Name</th>
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Value</th>
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($headers as $header)
                                    <tr class="border-b border-gray-100 last:border-0">
                                        <td class="px-3 py-2 font-mono text-xs text-gray-800">{{ $header['name'] }}</td>
                                        <td class="px-3 py-2 font-mono text-xs text-gray-500">{{ $header['value'] }}</td>
                                        <td class="px-3 py-2">
                                            @if($header['required'] ?? false)
                                                <x-ui.badge color="red">Required</x-ui.badge>
                                            @else
                                                <x-ui.badge color="gray">Optional</x-ui.badge>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Parameters --}}
            @if(!empty($params))
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Parameters / พารามิเตอร์</h4>
                    <div class="bg-gray-50 rounded-lg overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Name</th>
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Type</th>
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Required</th>
                                    <th class="text-left px-3 py-2 text-xs font-medium text-gray-500">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($params as $param)
                                    <tr class="border-b border-gray-100 last:border-0">
                                        <td class="px-3 py-2 font-mono text-xs text-gray-800">{{ $param['name'] }}</td>
                                        <td class="px-3 py-2">
                                            <x-ui.badge color="blue">{{ $param['type'] }}</x-ui.badge>
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($param['required'] ?? false)
                                                <x-ui.badge color="red">Required</x-ui.badge>
                                            @else
                                                <x-ui.badge color="gray">Optional</x-ui.badge>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-xs">
                                            <p class="text-gray-700">{{ $param['desc_en'] ?? '' }}</p>
                                            <p class="text-gray-400">{{ $param['desc_th'] ?? '' }}</p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Sample request body --}}
            @if($sampleBody)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Request Body Example / ตัวอย่าง Request Body</h4>
                    <pre class="bg-gray-900 text-green-400 rounded-lg p-4 text-xs font-mono overflow-x-auto">{{ $sampleBody }}</pre>
                </div>
            @endif

            {{-- Sample response --}}
            @if($sampleResponse)
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Response Example / ตัวอย่าง Response</h4>
                    <pre class="bg-gray-900 text-emerald-400 rounded-lg p-4 text-xs font-mono overflow-x-auto">{{ $sampleResponse }}</pre>
                </div>
            @endif

            {{-- Sandbox --}}
            <div class="border-t border-gray-100 pt-4">
                <button @click="sandbox = !sandbox"
                        class="flex items-center gap-2 text-sm font-medium transition-colors"
                        :class="sandbox ? 'text-(--color-primary)' : 'text-gray-500 hover:text-gray-700'">
                    <x-icon name="play" class="w-4 h-4" />
                    <span x-text="sandbox ? 'Hide Sandbox / ซ่อน Sandbox' : 'Open Sandbox / เปิด Sandbox'"></span>
                </button>

                <div x-show="sandbox" x-collapse x-cloak class="mt-4 space-y-3">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-2">
                        <p class="text-xs text-amber-700">
                            <strong>Sandbox Mode</strong> — Sends real API requests to the current server.
                            Ensure you have a valid token for authenticated endpoints.
                        </p>
                        <p class="text-xs text-amber-600">
                            <strong>โหมด Sandbox</strong> — ส่ง request จริงไปยังเซิร์ฟเวอร์ปัจจุบัน
                            ตรวจสอบว่ามี token ที่ใช้ได้สำหรับ endpoint ที่ต้องยืนยันตัวตน
                        </p>
                    </div>

                    {{-- URL --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">URL</label>
                        <input type="text" x-model="sandboxUrl"
                               class="w-full rounded-lg border-gray-300 text-sm font-mono px-3 py-2 border focus:border-(--color-primary) focus:ring-(--color-primary)">
                    </div>

                    {{-- Headers editor --}}
                    @if(!empty($headers))
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Headers</label>
                            <div class="space-y-2">
                                @foreach($headers as $header)
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono text-gray-600 w-36 flex-shrink-0">{{ $header['name'] }}:</span>
                                        <input type="text"
                                               x-model="sandboxHeaders['{{ $header['name'] }}']"
                                               placeholder="{{ $header['value'] }}"
                                               class="flex-1 rounded-lg border-gray-300 text-sm font-mono px-3 py-1.5 border focus:border-(--color-primary) focus:ring-(--color-primary)">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Body editor --}}
                    @if($method !== 'GET')
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Request Body (JSON)</label>
                            <textarea x-model="sandboxBody" rows="6"
                                      class="w-full rounded-lg border-gray-300 text-sm font-mono px-3 py-2 border focus:border-(--color-primary) focus:ring-(--color-primary)"></textarea>
                        </div>
                    @endif

                    {{-- Send button --}}
                    <div class="flex items-center gap-3">
                        <button @click="sendRequest()"
                                :disabled="loading"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-(--color-primary) text-white text-sm font-medium rounded-lg hover:bg-(--color-primary-dark) disabled:opacity-50 transition-colors">
                            <template x-if="loading">
                                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            <template x-if="!loading">
                                <x-icon name="play" class="w-4 h-4" />
                            </template>
                            <span x-text="loading ? 'Sending... / กำลังส่ง...' : 'Send Request / ส่ง Request'"></span>
                        </button>

                        <template x-if="responseStatus !== null">
                            <div class="flex items-center gap-2 text-sm">
                                <span :class="responseStatus >= 200 && responseStatus < 300 ? 'text-green-600' : 'text-red-600'" class="font-bold" x-text="'Status: ' + responseStatus"></span>
                                <span class="text-gray-400" x-text="responseTime + 'ms'"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Response --}}
                    <template x-if="responseBody">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-xs font-medium text-gray-500">Response / ผลลัพธ์</label>
                                <div class="flex items-center gap-1">
                                    <template x-if="responseStatus >= 200 && responseStatus < 300">
                                        <x-ui.badge color="green">
                                            <x-icon name="check-circle" class="w-3 h-3 mr-1" />
                                            Success
                                        </x-ui.badge>
                                    </template>
                                    <template x-if="responseStatus >= 400">
                                        <x-ui.badge color="red">
                                            <x-icon name="x-circle" class="w-3 h-3 mr-1" />
                                            Error
                                        </x-ui.badge>
                                    </template>
                                </div>
                            </div>
                            <pre class="bg-gray-900 rounded-lg p-4 text-xs font-mono overflow-x-auto max-h-96 overflow-y-auto"
                                 :class="responseStatus >= 200 && responseStatus < 300 ? 'text-emerald-400' : 'text-red-400'"
                                 x-text="responseBody"></pre>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
