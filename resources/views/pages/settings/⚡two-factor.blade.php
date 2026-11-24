<?php

use Laravel\Fortify\Features;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Two-factor authentication')] class extends Component {
    public function mount(): void
    {
        abort_unless(Features::enabled(Features::twoFactorAuthentication()), 403);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Two-factor authentication settings') }}</flux:heading>

    <x-pages::settings.layout
        :heading="__('Two-factor authentication')"
        :subheading="__('Manage your two-factor authentication settings')"
    >
        <div class="flex flex-col w-full mx-auto space-y-6 text-sm" wire:cloak>
            <div class="flex items-center gap-3">
                <flux:badge color="green">{{ __('Enabled') }}</flux:badge>
            </div>

            <flux:text>
                {{ __('When you sign in, enter the code from your authenticator app.') }}
            </flux:text>

            <livewire:pages::settings.two-factor.recovery-codes />
        </div>
    </x-pages::settings.layout>
</section>
