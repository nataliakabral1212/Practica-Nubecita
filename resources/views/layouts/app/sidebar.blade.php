<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    @php
        $lastDigitIsOdd = preg_match('/[13579][^0-9]*$/', (string) gethostname()) === 1;
    @endphp
    <body class="concert-stage dark min-h-screen" data-accent="{{ $lastDigitIsOdd ? 'fuchsia' : 'cyan' }}">
        <flux:sidebar sticky collapsible="mobile" class="concert-sidebar">
            <flux:sidebar.header>
                <a class="concert-brand" href="{{ route('dashboard') }}" wire:navigate>
                    <span class="concert-brand-icon"><flux:icon.musical-note variant="outline" /></span>
                    <span>{{ config('app.name') }}</span>
                </a>
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav aria-label="Navegación principal">
                <flux:sidebar.item icon="signal" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    Escenario
                </flux:sidebar.item>
                <flux:sidebar.item icon="adjustments-horizontal" :href="route('profile.edit')" :current="request()->is('settings/*')" wire:navigate>
                    Ajustes
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:spacer />

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="concert-mobile-header lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a class="concert-brand" href="{{ route('dashboard') }}" wire:navigate>{{ config('app.name') }}</a>
            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
        <x-server-badge />
    </body>
</html>
