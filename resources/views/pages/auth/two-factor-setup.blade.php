<x-layouts::auth :title="__('Set up two-factor authentication')">
    @if ($confirmed && $passwordConfirmed)
        <x-auth-header
            :title="__('Save your recovery codes')"
            :description="__('Store these codes in a secure place. Each code can be used once if you lose access to your authenticator.')"
        />

        <ul class="grid gap-1 rounded-lg bg-zinc-100 p-4 text-center font-mono text-sm dark:bg-white/5">
            @foreach ($recoveryCodes as $recoveryCode)
                <li>{{ $recoveryCode }}</li>
            @endforeach
        </ul>

        <flux:button :href="route('dashboard')" variant="primary">{{ __('Continue to dashboard') }}</flux:button>
    @else
        <x-auth-header
            :title="__('Set up two-factor authentication')"
            :description="__('You need to set up two-factor authentication to use your account.')"
        />

        @if (! $passwordConfirmed)
            <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-4">
                @csrf
                <flux:input name="password" :label="__('Password')" type="password" autocomplete="current-password" required viewable />
                @error('password')
                    <flux:text color="red">{{ $message }}</flux:text>
                @enderror
                <flux:button type="submit" variant="primary" class="w-full">{{ __('Confirm password') }}</flux:button>
            </form>
        @elseif (! $qrCode)
            <form method="POST" action="{{ route('two-factor.enable') }}">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">{{ __('Generate setup QR code') }}</flux:button>
            </form>
        @else
            <flux:text>{{ __('Scan this QR code with Google Authenticator or another authenticator app, then enter its six-digit code.') }}</flux:text>

            <div class="mx-auto rounded-lg bg-white p-3">
                {!! $qrCode !!}
            </div>

            <form method="POST" action="{{ route('two-factor.confirm') }}" class="space-y-4 text-center">
                @csrf
                <flux:otp name="code" length="6" :label="__('Authentication code')" label:sr-only class="mx-auto" />
                @error('code', 'confirmTwoFactorAuthentication')
                    <flux:text color="red">{{ $message }}</flux:text>
                @enderror
                @error('code')
                    <flux:text color="red">{{ $message }}</flux:text>
                @enderror
                <flux:button type="submit" variant="primary" class="w-full">{{ __('Confirm authentication code') }}</flux:button>
            </form>
        @endif
    @endif

    <form method="POST" action="{{ route('logout') }}" class="text-center">
        @csrf
        <flux:button type="submit" variant="ghost">{{ __('Log out') }}</flux:button>
    </form>
</x-layouts::auth>
