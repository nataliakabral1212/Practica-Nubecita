<x-layouts::app :title="__('Dashboard')">
    <section class="concert-ticket concert-hero" aria-labelledby="concert-heading">
        <div class="concert-hero-copy">
            <h1 class="concert-title" id="concert-heading">Escenario</h1>
            <dl>
                <dt>Atendido por</dt>
                <dd class="concert-host">{{ gethostname() }}</dd>
            </dl>
        </div>

        <div class="concert-ticket-stub" aria-hidden="true">
            <flux:icon.musical-note variant="outline" />
            <div class="concert-equalizer">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
        </div>
    </section>

    <nav class="concert-shortcuts" aria-label="Ajustes de la cuenta">
        <a class="concert-ticket concert-shortcut" href="{{ route('profile.edit') }}" wire:navigate>
            <flux:icon.user-circle variant="outline" class="concert-accent" />
            <div>
                <h2>Mi perfil</h2>
                <p>Actualiza tu nombre y correo.</p>
            </div>
            <flux:icon.arrow-up-right variant="micro" class="concert-accent" />
        </a>

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <a class="concert-ticket concert-shortcut" href="{{ route('two-factor.show') }}" wire:navigate>
                <flux:icon.shield-check variant="outline" class="concert-accent" />
                <div>
                    <h2>Seguridad 2FA</h2>
                    <p>Configura tu aplicación de autenticación.</p>
                </div>
                <flux:icon.arrow-up-right variant="micro" class="concert-accent" />
            </a>
        @endif
    </nav>
</x-layouts::app>
