<x-filament-panels::page>
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    {{-- Top Pages Table --}}
    <div class="flex flex-col gap-6">
        <div>
            @livewire(\App\Filament\Widgets\TopPagesTable::class)
        </div>

        {{-- Bottom Row: User Sources and Session Sources --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div>
                @livewire(\App\Filament\Widgets\UserSourcesTable::class)
            </div>
            <div>
                @livewire(\App\Filament\Widgets\SessionSourcesTable::class)
            </div>
        </div>
    </div>
</x-filament-panels::page>
