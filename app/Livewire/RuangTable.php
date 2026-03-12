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
use PowerComponents\LivewirePowerGrid\Concerns\Checkbox;

final class RuangTable extends PowerGridComponent
{
    use Checkbox; // Pastikan Trait Checkbox digunakan secara eksplisit di PG 6

    public string $tableName = 'ruang-table';

    /**
     * Helper authorization dengan bypass Superadmin
     */
    private function canManage(string $permission): bool
    {
        return auth()->user()->hasRole('superadmin') || auth()->user()->can($permission);
    }

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
     * PowerGrid 6: Tombol di sini akan muncul secara otomatis di header 
     * HANYA SAAT ada baris yang dicentang.
     */
    public function bulkActions(): array
    {
        $actions = [];

        if ($this->canManage('delete_ruang')) {
            $actions[] = Button::add('bulk-delete')
                ->slot('Hapus Terpilih (<span x-text="window.pgBulkActions.count(\'' . $this->tableName . '\')"></span>)')
                ->class('flex items-center gap-2 text-[11px] font-black uppercase tracking-widest text-rose-600 hover:text-rose-700 transition-all border border-rose-200 bg-rose-50 px-3 py-2 rounded-lg cursor-pointer')
                ->dispatch('confirmBulkDelete', []);
        }

        return $actions;
    }

    public function datasource(): Builder
    {
        return RefRuang::query();
    }

    #[On('confirmBulkDelete')]
    public function confirmBulkDelete(): void
    {
        // PowerGrid 6 menyimpan nilai terpilih di $this->checkboxValues
        $ids = $this->checkboxValues;

        if (empty($ids)) {
            $this->dispatch('toast', type: 'warning', message: 'Silakan pilih setidaknya satu ruangan.');
            return;
        }

        $this->dispatch('confirmDelete', [
            'id'    => $ids,
            'name'  => count($ids) . ' Ruangan terpilih',
            'event' => 'bulkDeleteRuang'
        ]);
    }

    #[On('bulkDeleteRuang')]
    public function bulkDeleteRuang(array $ids): void
    {
        if (!$this->canManage('delete_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Akses ditolak.');
            return;
        }

        try {
            DB::transaction(fn() => RefRuang::whereIn('id', $ids)->delete());

            $this->clearSelected(); // Membersihkan pilihan checkbox
            $this->dispatch('pg:eventRefresh-ruang-table');
            $this->dispatch('toast', type: 'success', message: count($ids) . ' Ruangan berhasil dihapus.');

        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menghapus data.');
        }
    }

    #[On('deleteRuang')]
    public function deleteRuang($id): void
    {
        if (!$this->canManage('delete_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Izin ditolak.');
            return;
        }

        try {
            RefRuang::findOrFail($id)->delete();
            $this->dispatch('pg:eventRefresh-ruang-table');
            $this->dispatch('toast', type: 'success', message: 'Ruangan berhasil dihapus.');
        } catch (\Exception $e) {
            $this->dispatch('toast', type: 'error', message: 'Gagal menghapus data.');
        }
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
            Column::action('Aksi')
        ];
    }

    public function actions(RefRuang $row): array
    {
        $actions = [];
        $canEdit = $this->canManage('edit_ruang');
        $canDelete = $this->canManage('delete_ruang');

        if ($canEdit) {
            $actions[] = Button::add('edit')
                ->slot('EDIT')
                ->class('text-[11px] font-black uppercase tracking-widest text-[#002855] hover:text-amber-600 transition-all border-0 bg-transparent cursor-pointer')
                ->dispatch('editRuang', ['id' => $row->id]);
        }

        if ($canDelete) {
            if ($canEdit) {
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

    /**
     * Listener untuk menangkap klik tombol EDIT dari baris tabel
     */
    #[On('editRuang')]
    public function edit($id): void
    {
        $realId = is_array($id) ? ($id['id'] ?? null) : $id;

        if (!$realId) return;

        if (!$this->canManage('edit_ruang')) {
            $this->dispatch('toast', type: 'error', message: 'Izin ditolak.');
            return;
        }

        $this->dispatch('openEditForm', id: $realId)->to(RuangManager::class);
    }
}