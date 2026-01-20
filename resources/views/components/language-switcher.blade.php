@props(['class' => ''])

<div x-data="{ langOpen: false }" class="relative {{ $class }}">
    <button @click="langOpen = !langOpen" type="button"
        class="inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer"
        aria-label="{{ __('Change language') }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
        </svg>
        <span class="hidden sm:inline">{{ app()->getLocale() === 'hu' ? 'HU' : 'EN' }}</span>
    </button>

    <div x-show="langOpen" x-cloak @click.outside="langOpen = false" x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute right-0 mt-2 w-36 rounded-lg shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black/5 dark:ring-white/10 z-50">
        <div class="py-1" role="menu">
            <a href="{{ route('language.switch', 'en') }}"
                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ app()->getLocale() === 'en' ? 'bg-gray-100 dark:bg-gray-700 font-medium' : '' }}"
                role="menuitem">
                English
            </a>
            <a href="{{ route('language.switch', 'hu') }}"
                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors {{ app()->getLocale() === 'hu' ? 'bg-gray-100 dark:bg-gray-700 font-medium' : '' }}"
                role="menuitem">
                Magyar
            </a>
        </div>
    </div>
</div>
