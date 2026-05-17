@extends('layouts.app')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-3xl mx-auto py-6 space-y-6">

    {{-- Update Profile Info --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-1">Profile Information</h2>
        <p class="text-sm text-gray-500 mb-6">Update your name, email address and phone number.</p>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('patch')

            {{-- Avatar --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                <div class="flex items-center gap-4">
                    <img src="{{ auth()->user()->avatar_url }}"
                        class="w-16 h-16 rounded-full object-cover border border-gray-200">
                    <input type="file" name="avatar" accept="image/*"
                        class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                </div>
                @error('avatar')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    Save Changes
                </button>
                @if(session('status') === 'profile-updated')
                    <p class="text-sm text-green-600">Saved successfully!</p>
                @endif
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-1">Change Password</h2>
        <p class="text-sm text-gray-500 mb-6">Make sure to use a strong password.</p>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                @error('current_password', 'updatePassword')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="password"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                @error('password', 'updatePassword')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="flex items-center gap-4 pt-2">
                <button type="submit"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                    Update Password
                </button>
                @if(session('status') === 'password-updated')
                    <p class="text-sm text-green-600">Password updated!</p>
                @endif
            </div>
        </form>
    </div>


     {{-- MFA Toggle --}}
     <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
         <h2 class="text-base font-semibold text-gray-900 mb-1">Two-Factor Authentication</h2>
         <p class="text-sm text-gray-500 mb-4">
             Add an extra layer of security. When enabled, you'll receive a 6-digit code via email each time you log in.
         </p>

         <div class="flex items-center justify-between">
             <span class="text-sm font-medium {{ auth()->user()->mfa_enabled ? 'text-green-600' : 'text-gray-500' }}">
                 {{ auth()->user()->mfa_enabled ? 'Enabled' : 'Disabled' }}
             </span>

             <form method="POST" action="{{ route('mfa.toggle') }}">
                 @csrf
                 <button type="submit"
                     class="{{ auth()->user()->mfa_enabled
                         ? 'bg-red-100 text-red-700 hover:bg-red-200'
                         : 'bg-green-100 text-green-700 hover:bg-green-200' }}
                         text-sm font-medium px-4 py-2 rounded-lg transition">
                     {{ auth()->user()->mfa_enabled ? 'Disable MFA' : 'Enable MFA' }}
                 </button>
             </form>
         </div>
     </div>


    {{-- Delete Account --}}
    <div class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
        <h2 class="text-base font-semibold text-red-600 mb-1">Delete Account</h2>
        <p class="text-sm text-gray-500 mb-6">Once deleted, all data will be permanently removed.</p>

        <form method="POST" action="{{ route('profile.destroy') }}"
            onsubmit="return confirm('Are you sure? This cannot be undone.')">
            @csrf
            @method('delete')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm your password</label>
                <input type="password" name="password"
                    class="w-full border border-red-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                @error('password', 'userDeletion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                Delete My Account
            </button>
        </form>
    </div>

</div>
@endsection