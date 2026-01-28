
@props(['route', 'icon', 'label'])
<a href="{{ route($route) }}" 
   class="flex items-center px-4 py-3.5 rounded-2xl text-sm font-bold transition-all duration-300 group
   {{ request()->routeIs($route) ? 'nav-link-active' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
    <svg class="w-5 h-5 mr-4 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
    </svg>
    {{ $label }}
</a>
