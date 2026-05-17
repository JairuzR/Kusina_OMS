<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-xl font-bold text-gray-800">Two-Factor Verification</h2>
        <p class="text-sm text-gray-500 mt-1">Enter the 6-digit code sent to your email.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('mfa.verify') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="code" value="Verification Code" />
            <x-text-input
                id="code"
                name="code"
                type="text"
                class="mt-1 block w-full text-center text-2xl tracking-widest font-bold"
                maxlength="6"
                autofocus
                autocomplete="one-time-code"
                inputmode="numeric"
                pattern="[0-9]{6}"
                placeholder="000000"
            />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center mb-3">
            Verify Code
        </x-primary-button>
    </form>

    <form method="POST" action="{{ route('mfa.resend') }}">
        @csrf
        <button type="submit"
            class="w-full text-sm text-orange-500 hover:text-orange-600 text-center transition">
            Resend Code
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">
            ← Back to Login
        </a>
    </div>
</x-guest-layout>