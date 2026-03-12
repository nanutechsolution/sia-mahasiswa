<div class="flex justify-end items-center gap-3">
    {{-- 1. TOMBOL EDIT --}}
    @if(isset($editPermission))
        @can($editPermission)
            @if(isset($editUrl))
                <a href="{{ $editUrl }}" class="text-[11px] font-black uppercase tracking-widest text-unmaris-blue hover:text-unmaris-gold transition-colors">
                    Edit
                </a>
            @else
                <button wire:click="$dispatch('{{ $editEvent }}', { id: {{ $id }} })" 
                        class="text-[11px] font-black uppercase tracking-widest text-unmaris-blue hover:text-unmaris-gold transition-colors border-0 bg-transparent cursor-pointer">
                    Edit
                </button>
            @endif
        @endcan
    @endif

    {{-- DIVIDER --}}
    @if(isset($editPermission) && auth()->user()->can($editPermission) && isset($deletePermission) && auth()->user()->can($deletePermission))
        <span class="text-slate-300">|</span>
    @endif

    {{-- 2. TOMBOL DELETE --}}
    @if(isset($deletePermission))
        @can($deletePermission)
            <button x-data x-on:click="
                Swal.fire({
                    title: '{{ $deleteTitle ?? 'Hapus data ini?' }}',
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    iconColor: 'var(--color-unmaris-gold)',
                    background: 'var(--color-unmaris-light)',
                    color: 'var(--color-unmaris-dark)',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: 'var(--color-unmaris-blue)',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl border border-slate-200 shadow-2xl',
                        title: 'font-black text-xl text-[#002855]',
                        confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                        cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('{{ $deleteEvent }}', { id: {{ $id }} });
                    }
                })
            " class="text-[11px] font-black uppercase tracking-widest text-rose-600 hover:text-rose-700 transition-colors border-0 bg-transparent cursor-pointer">
                Hapus
            </button>
        @endcan
    @endif
</div>