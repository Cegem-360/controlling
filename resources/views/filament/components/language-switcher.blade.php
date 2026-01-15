<div x-data="{ open: false }" class="relative">
    @vite('resources/js/app.js')
    <button x-on:click="open = !open" type="button"
        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
        <x-heroicon-o-language class="h-5 w-5" />
        <span>{{ app()->getLocale() === 'hu' ? 'HU' : 'EN' }}</span>
        <x-heroicon-m-chevron-down class="h-4 w-4" />
    </button>

    <div x-show="open" x-on:click.outside="open = false" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-10 mt-2 w-32 origin-top-right rounded-lg bg-white shadow-lg ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
        x-cloak>
        <div class="p-1">
            <a href="{{ url('/language/en') }}"
                class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 {{ app()->getLocale() === 'en' ? 'bg-gray-100 dark:bg-gray-800 font-medium' : '' }}">
                English
            </a>
            <a href="{{ url('/language/hu') }}"
                class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800 {{ app()->getLocale() === 'hu' ? 'bg-gray-100 dark:bg-gray-800 font-medium' : '' }}">
                Magyar
            </a>
        </div>
    </div>
</div>
