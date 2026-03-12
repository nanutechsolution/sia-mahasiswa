<?php

namespace App\Livewire;

use App\Models\RefRuang;
use App\Livewire\Admin\Master\RuangManager;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

final class RuangTable extends PowerGridComponent
{
    public string $tableName = 'ruang-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput()
                ->showToggleColumns(),
            
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    /**
     * Sesuai Dokumentasi PowerGrid 6+: 
     * Tombol ini akan muncul secara otomatis di header saat baris dipilih.
     */
    public function bulkActions(): array
    {
        return [
            Button::add('bulk-delete')
                ->slot('Hapus Terpilih')
                ->class('text-[11px] font-black uppercase tracking-widest text-rose-600 hover:text-rose-700 transition-all border border-rose-200 bg-rose-50 px-3 py-2 rounded-lg cursor-pointer')
                ->dispatch('confirmBulkDelete', []),
        ];
    }

    public function datasource(): Builder
    {
        return RefRuang::query();
    }

    /**
     * Metode ini dipanggil saat tombol "Hapus Terpilih" diklik.
     * Mengambil ID dari properti $checkboxValues bawaan PowerGrid.
     */
    #[On('confirmBulkDelete')]
    public function confirmBulkDelete(): void
    {
        $ids = $this->checkboxValues;

        if (empty($ids)) {
            $this->dispatch('toast', type: 'warning', message: 'Silakan pilih setidaknya satu ruangan terlebih dahulu.');
            return;
        }

        $this->dispatch('confirmDelete', [
            'id'    => $ids,
            'name'  => count($ids) . ' Ruangan yang dipilih',
            'event' => 'bulkDeleteRuang'
        ]);
    }

    /**
     * Eksekusi penghapusan masal setelah konfirmasi "Ya" dari SweetAlert
     */
    #[On('bulkDeleteRuang')]
    public function bulkDeleteRuang(array $ids): void
    {
        if (!auth()->user()->can('delete_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Akses ditolak: Anda tidak memiliki izin menghapus data.');
            return;
        }

        try {
            DB::beginTransaction();

            $items = RefRuang::whereIn('id', $ids)->get();

            foreach ($items as $item) {
                // Pagar Pengaman: Cek relasi jika perlu
                // if ($item->jadwals()->exists()) { ... }
                $item->delete();
            }

            DB::commit();

            $this->clearSelected(); // Penting: Membersihkan checkbox setelah sukses
            
            $this->dispatch('pg:eventRefresh-ruang-table');
            $this->dispatch('toast', type: 'success', message: count($ids) . ' Ruangan berhasil dihapus secara masal.');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    /**
     * Aksi Hapus Tunggal
     */
    #[On('deleteRuang')]
    public function deleteRuang($id): void
    {
        if (!auth()->user()->can('delete_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Izin ditolak.');
            return;
        }

        try {
            $ruang = RefRuang::find($id);
            if (!$ruang) return;

            DB::beginTransaction();
            $ruang->delete();
            DB::commit();

            $this->dispatch('pg:eventRefresh-ruang-table');
            $this->dispatch('toast', type: 'success', message: 'Ruangan berhasil dihapus.');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('toast', type: 'error', message: 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Simpan data baru (Insert MySQL)
     */
    #[On('storeRuang')]
    public function storeRuang(array $data): void
    {
        if (!auth()->user()->can('create_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Izin ditolak.');
            return;
        }

        try {
            DB::beginTransaction();
            RefRuang::create([
                'kode_ruang' => strtoupper($data['kode_ruang']),
                'nama_ruang' => $data['nama_ruang'],
                'kapasitas'  => $data['kapasitas'] ?? 40,
                'is_active'  => $data['is_active'] ?? true,
            ]);
            DB::commit();

            $this->dispatch('pg:eventRefresh-ruang-table');
            $this->dispatch('toast', type: 'success', message: 'Data ruangan berhasil disimpan ke MySQL.');
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('toast', type: 'error', message: 'Terjadi kesalahan saat menyimpan.');
        }
    }

    #[On('editRuang')]
    public function edit($id): void
    {
        if (!auth()->user()->can('edit_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Izin ditolak.');
            return;
        }
        $this->dispatch('openEditForm', id: $id)->to(RuangManager::class);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('kode_html', function ($model) {
                return '<span class="font-mono text-[11px] font-black text-[#002855] bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">' . $model->kode_ruang . '</span>';
            })
            ->add('nama_ruang', fn($model) => '<div class="text-sm font-bold text-slate-800">' . $model->nama_ruang . '</div>')
            ->add('kapasitas_label', fn($model) => '<span class="text-xs font-bold text-slate-600">' . $model->kapasitas . ' <span class="text-[10px] text-slate-400 font-medium">Mhs</span></span>')
            ->add('status_html', fn($model) => view('components.badge-status', [
                'type' => $model->is_active ? 'success' : 'danger',
                'label' => $model->is_active ? 'Aktif' : 'Non-Aktif'
            ])->render());
    }

    public function columns(): array
    {
        return [
            Column::make('Kode Ruang', 'kode_html', 'kode_ruang')->sortable()->searchable(),
            Column::make('Nama Ruangan', 'nama_ruang')->sortable()->searchable(),
            Column::make('Kapasitas', 'kapasitas_label', 'kapasitas')->sortable()->bodyAttribute('text-center'),
            Column::make('Status', 'status_html', 'is_active')->sortable()->bodyAttribute('text-center'),
            Column::action('Aksi')->headerAttribute('text-left')->bodyAttribute('text-left')
        ];
    }

    public function actions(RefRuang $row): array
    {
        $actions = [];

        if (auth()->user()->can('edit_ruang')) {
            $actions[] = Button::add('edit')
                ->slot('EDIT')
                ->class('text-[11px] font-black uppercase tracking-widest text-[#002855] hover:text-unmaris-gold transition-all border-0 bg-transparent cursor-pointer')
                ->dispatch('editRuang', ['id' => $row->id]);
        }

        if (auth()->user()->can('delete_ruang')) {
            if (count($actions) > 0) {
                $actions[] = Button::add('spacer')->slot('<span class="text-slate-300">|</span>')->class('cursor-default pointer-events-none px-1');
            }
            $actions[] = Button::add('delete')
                ->slot('HAPUS')
                ->class('text-[11px] font-black uppercase tracking-widest text-rose-600 hover:text-rose-700 transition-all border-0 bg-transparent cursor-pointer')
                ->dispatch('confirmDelete', [
                    'id'    => $row->id,
                    'name'  => $row->nama_ruang,
                    'event' => 'deleteRuang'
                ]);
        }

        return $actions;
    }

    public function loadingSlot(): ?string
    {
        return <<<'HTML'
            <div class="p-6 space-y-4 animate-pulse">
                <div class="flex items-center space-x-4">
                    <div class="h-4 bg-slate-100 rounded-lg w-12"></div>
                    <div class="h-4 bg-slate-100 rounded-lg flex-1"></div>
                    <div class="h-4 bg-slate-100 rounded-lg w-32"></div>
                    <div class="h-4 bg-slate-100 rounded-lg w-24"></div>
                </div>
            </div>
        HTML;
    }
}