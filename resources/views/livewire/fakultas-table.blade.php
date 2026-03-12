<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-unmaris-blue">Master Fakultas</h1>
            <p class="text-slate-500 text-sm mt-1">Data unit fakultas di lingkungan universitas.</p>
        </div>
        @if(!$showForm)
        <button wire:click="create" class="inline-flex items-center px-5 py-2.5 bg-unmaris-blue text-white rounded-xl font-black text-sm shadow-lg shadow-unmaris-blue-500/20 hover:bg-unmaris-yellow hover:scale-105 transition-all hover:text-unmaris-blue">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Fakultas
        </button>
        @endif
    </div>
    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <livewire:fakultas-table />
    </div>
</div>