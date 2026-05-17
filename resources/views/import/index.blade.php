@extends('layouts.app')
@section('title', 'Import & Export — KusinaOMS')
@section('page-title', 'Import & Export')

@section('content')
<div class="mt-4 space-y-6">

    {{-- Import Errors --}}
    @if(session('import_errors'))
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <p class="text-sm font-semibold text-yellow-800 mb-2">Some rows could not be imported:</p>
        <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside max-h-40 overflow-y-auto">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Menu Items Import --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="font-semibold text-gray-900">Import Menu Items</h2>
                <p class="text-xs text-gray-500 mt-0.5">Bulk upload menu items from a CSV file</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700 space-y-1">
                    <p class="font-semibold">Required columns:</p>
                    <p>name, category, price</p>
                    <p class="font-semibold mt-1">Optional columns:</p>
                    <p>description, cost_price, preparation_time, calories, is_available, is_featured</p>
                </div>
                <a href="{{ route('import.template.menu') }}"
                   class="inline-flex items-center gap-2 text-sm text-orange-500 hover:text-orange-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
                <form method="POST" action="{{ route('import.menu') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                        <input type="file" name="file" accept=".csv,.txt"
                               class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                            class="w-full px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition">
                        Import Menu Items
                    </button>
                </form>
            </div>
        </div>

        {{-- Inventory Import --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h2 class="font-semibold text-gray-900">Import Inventory</h2>
                <p class="text-xs text-gray-500 mt-0.5">Bulk upload inventory items from a CSV file</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-xs text-blue-700 space-y-1">
                    <p class="font-semibold">Required columns:</p>
                    <p>name, unit, quantity</p>
                    <p class="font-semibold mt-1">Optional columns:</p>
                    <p>sku, category, min_quantity, cost_per_unit, supplier, storage_location, expiry_date</p>
                </div>
                <a href="{{ route('import.template.inventory') }}"
                   class="inline-flex items-center gap-2 text-sm text-orange-500 hover:text-orange-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Template
                </a>
                <form method="POST" action="{{ route('import.inventory') }}" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                        <input type="file" name="file" accept=".csv,.txt"
                               class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit"
                            class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        Import Inventory
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- Export Section --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h2 class="font-semibold text-gray-900">Export Data</h2>
            <p class="text-xs text-gray-500 mt-0.5">Download your data as CSV files</p>
        </div>
        <div class="p-6 grid sm:grid-cols-3 gap-4">
            <a href="{{ route('reports.export', ['type' => 'sales', 'period' => 'this_month']) }}"
               class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:border-orange-300 hover:bg-orange-50 transition group">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Orders</p>
                    <p class="text-xs text-gray-400">This month's orders</p>
                </div>
            </a>
            <a href="{{ route('reports.export', ['type' => 'inventory']) }}"
               class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition group">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Inventory</p>
                    <p class="text-xs text-gray-400">All inventory items</p>
                </div>
            </a>
            <a href="{{ route('reports.export', ['type' => 'top_items', 'period' => 'this_month']) }}"
               class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl hover:border-green-300 hover:bg-green-50 transition group">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">Top Items</p>
                    <p class="text-xs text-gray-400">Best sellers this month</p>
                </div>
            </a>
        </div>
    </div>

</div>
@endsection