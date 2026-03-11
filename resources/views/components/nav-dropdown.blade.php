@props(['active', 'title', 'icon' => null])

<div x-data="{ open: @js($active) }" class="w-full">
    <button @click="open = !open"
        class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-white transition-all duration-200 {{ $active ? 'bg-white/5 text-white' : '' }}">
        <div class="flex items-center">
            @if($icon)
            <div class="mr-3 flex-shrink-0 {{ $active ? 'text-unmaris-gold' : 'text-slate-500' }}">
                <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5" />
            </div>
            @endif
            <span class="text-sm tracking-wide">{{ $title }}</span>
        </div>
        <x-heroicon-o-chevron-down
            class="w-4 h-4 transition-transform duration-200"
            ::class="open ? 'rotate-180' : ''" />
    </button>

    <div x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-2"
        class="pl-12 space-y-1 mt-1 border-l border-white/10 ml-6">
        {{ $slot }}
    </div>
</div>