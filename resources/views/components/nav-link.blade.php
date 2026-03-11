@props(['active', 'icon' => null, 'wire' => true])

@php
$classes = ($active ?? false)
            ? 'bg-white/10 text-white font-semibold shadow-sm'
            : 'text-slate-400 hover:bg-white/5 hover:text-white transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => 'flex items-center px-4 py-2.5 rounded-xl group ' . $classes]) }} {{ $wire ? 'wire:navigate' : '' }}>
    @if($icon)
        {{-- Jika icon dikirim sebagai string (nama Heroicon), kita render secara dinamis --}}
        <div class="mr-3 flex-shrink-0 {{ ($active ?? false) ? 'text-unmaris-gold' : 'text-slate-500 group-hover:text-unmaris-gold' }}">
            <x-dynamic-component 
                :component="'heroicon-o-' . $icon" 
                class="w-5 h-5 transition-colors duration-200" 
            />
        </div>
    @endif
    
    <span class="truncate text-sm tracking-wide">{{ $slot }}</span>
</a>