<x-guest-layout>
    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf

        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        <div>
            <x-input-label for="otp" value="Enter OTP" />
            <x-text-input id="otp" class="block mt-1 w-full"
                type="text" name="otp" maxlength="6"
                required autofocus autocomplete="one-time-code" />
            <p class="mt-1 text-xs text-gray-500">
                Check your @crestecphil.com.ph inbox. OTP expires in 10 minutes.
            </p>
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>Verify OTP</x-primary-button>
        </div>
    </form>
</x-guest-layout>
