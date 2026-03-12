@props(['type' => 'default', 'label' => ''])

@php
    $classes = [
        'success' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'danger'  => 'bg-rose-100 text-rose-700 border-rose-200',
        'warning' => 'bg-amber-100 text-amber-700 border-amber-200',
        'info'    => 'bg-sky-100 text-sky-700 border-sky-200',
        'default' => 'bg-slate-100 text-slate-700 border-slate-200',
    ][$type] ?? 'bg-slate-100 text-slate-700 border-slate-200';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide border $classes"]) }}>
    {{ $label }}
</span>