<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="concert-main">
        <div class="concert-content">{{ $slot }}</div>
    </flux:main>
</x-layouts::app.sidebar>
