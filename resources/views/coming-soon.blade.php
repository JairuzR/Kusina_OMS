@extends('layouts.app')

@section('title', $page . ' - KusinaOMS')
@section('page-title', $page)

@section('content')
<div class="flex items-center justify-center h-64">
    <div class="text-center">
        <p class="text-4xl font-bold text-gray-300 mb-2">Coming Soon</p>
        <p class="text-gray-500">The {{ $page }} module is being built.</p>
    </div>
</div>
@endsection