<div class="flex justify-end gap-2">
    <button wire:click="$dispatch('editProdi', { id: {{ $id }} })" class="p-2 text-[#002855] hover:bg-[#002855]/10 rounded-lg transition-colors border-0" title="Edit">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
    </button>

   <button x-data x-on:click="
        Swal.fire({
            title: 'Hapus Prodi ini?',
            text: 'Data terkait prodi ini mungkin akan mengalami error.',
            icon: 'warning',
            iconColor: 'var(--color-unmaris-gold)', /* Ikon warna emas */
            background: 'var(--color-unmaris-light)', /* Background terang */
            color: 'var(--color-unmaris-dark)', /* Teks gelap */
            showCancelButton: true,
            confirmButtonColor: '#e11d48', /* Tetap rose-600 untuk bahaya */
            cancelButtonColor: 'var(--color-unmaris-blue)', /* Tombol batal pakai biru UNMARIS */
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl border border-slate-200 shadow-2xl',
                title: 'font-black text-xl text-[#002855]',
                confirmButton: 'rounded-xl px-6 py-2.5 font-bold shadow-lg',
                cancelButton: 'rounded-xl px-6 py-2.5 font-bold shadow-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('deleteProdi', { id: {{ $id }} });
            }
        })
    " class="p-2 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors border-0" title="Hapus">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
    </button>
</div>