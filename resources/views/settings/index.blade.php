@extends('layouts.app')
@section('title', 'Settings — KusinaOMS')
@section('page-title', 'Site Settings')

@section('content')
<div class="mt-4">
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        <div class="space-y-6">

            {{-- General --}}
            @if(isset($settings['general']))
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-semibold text-gray-900">General</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Basic restaurant information</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    @foreach($settings['general'] as $setting)
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-medium text-gray-700">
                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                        </label>
                        <div class="col-span-2">
                            <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                            <input type="text"
                                   name="settings[{{ $loop->index }}][value]"
                                   value="{{ $setting->value }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Billing --}}
            @if(isset($settings['billing']))
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-semibold text-gray-900">Billing</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Tax rate and currency configuration</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    @php $loopOffset = ($settings['general'] ?? collect())->count(); @endphp
                    @foreach($settings['billing'] as $setting)
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-medium text-gray-700">
                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                        </label>
                        <div class="col-span-2 flex items-center gap-2">
                            <input type="hidden" name="settings[{{ $loopOffset + $loop->index }}][key]" value="{{ $setting->key }}">
                            <input type="{{ $setting->type === 'integer' ? 'number' : 'text' }}"
                                   name="settings[{{ $loopOffset + $loop->index }}][value]"
                                   value="{{ $setting->value }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                            @if($setting->key === 'tax_rate')
                                <span class="text-sm text-gray-500 flex-shrink-0">%</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Security --}}
            @if(isset($settings['security']))
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-semibold text-gray-900">Security</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Session and login security settings</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    @php
                        $prevCount = ($settings['general'] ?? collect())->count()
                                   + ($settings['billing'] ?? collect())->count();
                    @endphp
                    @foreach($settings['security'] as $setting)
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <div>
                            <label class="text-sm font-medium text-gray-700">
                                {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                            </label>
                            @if($setting->key === 'session_timeout')
                                <p class="text-xs text-gray-400">In minutes</p>
                            @endif
                        </div>
                        <div class="col-span-2">
                            <input type="hidden" name="settings[{{ $prevCount + $loop->index }}][key]" value="{{ $setting->key }}">
                            <input type="number"
                                   name="settings[{{ $prevCount + $loop->index }}][value]"
                                   value="{{ $setting->value }}"
                                   min="1"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Alerts --}}
            @if(isset($settings['alerts']))
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-semibold text-gray-900">Alerts</h2>
                    <p class="text-xs text-gray-500 mt-0.5">System alert preferences</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    @php
                        $prevCount = ($settings['general'] ?? collect())->count()
                                   + ($settings['billing'] ?? collect())->count()
                                   + ($settings['security'] ?? collect())->count();
                    @endphp
                    @foreach($settings['alerts'] as $setting)
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-medium text-gray-700">
                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                        </label>
                        <div class="col-span-2">
                            <input type="hidden" name="settings[{{ $prevCount + $loop->index }}][key]" value="{{ $setting->key }}">
                            @if($setting->type === 'boolean')
                                <select name="settings[{{ $prevCount + $loop->index }}][value]"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition appearance-none pr-8 bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[right_0.5rem_center] bg-[length:1.25rem]">
                                    <option value="true"  @selected($setting->value === 'true')>Enabled</option>
                                    <option value="false" @selected($setting->value === 'false')>Disabled</option>
                                </select>
                            @else
                                <input type="text"
                                       name="settings[{{ $prevCount + $loop->index }}][value]"
                                       value="{{ $setting->value }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition">
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Backup --}}
            @if(isset($settings['backup']))
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="font-semibold text-gray-900">Backup</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Automated backup schedule</p>
                </div>
                <div class="px-6 py-5 space-y-4">
                    @php
                        $prevCount = ($settings['general'] ?? collect())->count()
                                   + ($settings['billing'] ?? collect())->count()
                                   + ($settings['security'] ?? collect())->count()
                                   + ($settings['alerts'] ?? collect())->count();
                    @endphp
                    @foreach($settings['backup'] as $setting)
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <label class="text-sm font-medium text-gray-700">
                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                        </label>
                        <div class="col-span-2">
                            <input type="hidden" name="settings[{{ $prevCount + $loop->index }}][key]" value="{{ $setting->key }}">
                            <select name="settings[{{ $prevCount + $loop->index }}][value]"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 bg-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition appearance-none pr-8 bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20fill%3D%22none%22%20viewBox%3D%220%200%2020%2020%22%3E%3Cpath%20stroke%3D%22%236b7280%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%20stroke-width%3D%221.5%22%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[right_0.5rem_center] bg-[length:1.25rem]">
                                <option value="daily"   @selected($setting->value === 'daily')>Daily</option>
                                <option value="weekly"  @selected($setting->value === 'weekly')>Weekly</option>
                                <option value="monthly" @selected($setting->value === 'monthly')>Monthly</option>
                            </select>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Save Button --}}
        <div class="mt-6 flex justify-end">
            <button type="submit"
                    class="px-6 py-2.5 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection