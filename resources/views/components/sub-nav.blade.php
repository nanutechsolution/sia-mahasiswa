
@props(['route', 'label'])
<a href="{{ route($route) }}" 
   class="block py-2 text-xs font-bold transition-all duration-200 
   {{ request()->routeIs($route) ? 'sub-nav-active' : 'text-slate-500 hover:text-unmaris-yellow' }}">
    {{ $label }}
</a>