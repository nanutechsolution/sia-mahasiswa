<?php

namespace App\Livewire;

use App\Domains\Core\Models\Prodi;
use App\Livewire\Admin\Master\ProdiManager;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

final class ProdiTable extends PowerGridComponent
{
    public string $tableName = 'prodi-table';

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        // Eager loading relasi untuk performa maksimal
        return Prodi::query()->with(['fakultas', 'kaprodiAktif.person.gelars']);
    }

    /**
     * Listener untuk menangkap aksi hapus dari SweetAlert
     */
    #[On('deleteProdi')]
    public function deleteProdi($id): void
    {
        // 1. Cek Izin Otorisasi
        if (!auth()->user()->can('delete_prodi')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak memiliki izin untuk menghapus data ini.');
            return;
        }

        try {
            // Mulai Transaksi di awal agar seluruh pengecekan berada dalam satu scope yang aman
            DB::beginTransaction();
            // 2. Cari data (Gunakan find agar tidak melempar Exception otomatis jika tidak ada)
            $prodi = Prodi::find($id);
            
            if (!$prodi) {
                // Jika data memang sudah tidak ada, batalkan transaksi dan beritahu user
                DB::rollBack();
                $this->dispatch('toast', type: 'error', message: 'Gagal! Data tidak ditemukan atau sudah dihapus sebelumnya.');
                $this->dispatch('pg:eventRefresh-prodi-table');
                return;
            }

            /**
             * 3. PAGAR PENGAMAN: Cek Relasi Terikat
             * Memastikan data tidak dihapus jika masih memiliki mahasiswa atau relasi lain.
             */
            
            // Cek Mahasiswa
            if ($prodi->mahasiswas()->exists()) {
                $count = $prodi->mahasiswas()->count();
                // Batalkan transaksi sebelum melempar error
                DB::rollBack();
                $this->dispatch('toast', type: 'error', message: "Gagal! Program Studi {$prodi->nama_prodi} masih memiliki {$count} mahasiswa aktif.");
                return;
            }

            // 4. Eksekusi Hapus
            $prodi->delete();

            // Selesaikan transaksi
            DB::commit();

            // 5. Refresh Tabel & Feedback
            $this->dispatch('pg:eventRefresh-prodi-table');
            $this->dispatch('toast', type: 'success', message: 'Program Studi berhasil dihapus permanen.');

        } catch (\Exception $e) {
            // Pastikan rollback dilakukan jika terjadi error sistem
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            $this->dispatch('toast', type: 'error', message: 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Listener untuk aksi edit
     */
    #[On('editProdi')]
    public function editProdi($id): void
    {
        $this->dispatch('openEditForm', id: $id)->to(ProdiManager::class);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('kode_html', function ($model) {
                $html = '<span class="font-mono text-[11px] font-black text-[#002855] bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">' . $model->kode_prodi_internal . '</span>';
                if ($model->kode_prodi_dikti) {
                    $html .= '<div class="text-[9px] text-slate-400 mt-1 font-mono uppercase">Dikti: ' . $model->kode_prodi_dikti . '</div>';
                }
                return $html;
            })
            ->add('nama_prodi_html', function ($model) {
                $html = '<div class="text-sm font-bold text-slate-800">' . $model->nama_prodi . '</div>';
                $kaprodi = $model->nama_kaprodi ?? '-';
                if ($kaprodi !== '-') {
                    $html .= '<div class="text-[10px] text-slate-500 mt-0.5 font-medium flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                Kaprodi: ' . $kaprodi . '
                              </div>';
                }
                return $html;
            })
            ->add('nama_fakultas', fn($model) => $model->fakultas->nama_fakultas ?? '-')
            ->add('jenjang_html', fn($model) => view('components.badge-status', ['type' => 'info', 'label' => $model->jenjang])->render())
            ->add('status_html', fn($model) => view('components.badge-status', [
                'type' => $model->is_active ? 'success' : 'danger',
                'label' => $model->is_active ? 'Aktif' : 'Non-Aktif'
            ])->render());
    }

    public function columns(): array
    {
        return [
            Column::make('Kode', 'kode_html', 'kode_prodi_internal')
                ->sortable()
                ->searchable(),

            Column::make('Program Studi', 'nama_prodi_html', 'nama_prodi')
                ->sortable()
                ->searchable(),

            Column::make('Fakultas', 'nama_fakultas'),

            Column::make('Jenjang', 'jenjang_html', 'jenjang')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::make('Status', 'status_html', 'is_active')
                ->sortable()
                ->bodyAttribute('text-center'),

            Column::action('Aksi')
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left')
        ];
    }

    public function actions(Prodi $row): array
    {
        $actions = [];

        if (auth()->user()->can('edit_prodi')) {
            $actions[] = Button::add('edit')
                ->slot('EDIT')
                ->class('text-[11px] font-black uppercase tracking-widest text-[#002855] hover:text-unmaris-gold transition-all border-0 bg-transparent cursor-pointer')
                ->dispatch('editProdi', ['id' => $row->id]);
        }
        if (auth()->user()->can('delete_prodi')) {
            if (count($actions) > 0) {
                $actions[] = Button::add('spacer')
                    ->slot('<span class="text-slate-300">|</span>')
                    ->class('cursor-default pointer-events-none px-1');
            }
            $actions[] = Button::add('delete')
                ->slot('HAPUS')
                ->class('text-[11px] font-black uppercase tracking-widest text-rose-600 hover:text-rose-700 transition-all border-0 bg-transparent cursor-pointer')
                ->dispatch('confirmDelete', [
                    'id'    => $row->id,
                    'name'  => $row->nama_prodi,
                    'event' => 'deleteProdi'
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