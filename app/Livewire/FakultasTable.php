<?php

namespace App\Livewire;

use App\Domains\Core\Models\Fakultas;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

final class FakultasTable extends PowerGridComponent
{
    public string $tableName = 'fakultas-table';

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
        // Eager loading relasi dekan untuk performa
        return Fakultas::query()->with(['dekanAktif.person.gelars']);
    }

    /**
     * Listener untuk menangkap aksi hapus dari SweetAlert
     */
    #[On('deleteFakultas')]
    public function deleteFakultas($id): void
    {
        if (!auth()->user()->can('delete_fakultas')) {
            $this->dispatch('toast', type: 'error', message: 'Anda tidak memiliki izin untuk menghapus data ini.');
            return;
        }

        try {
            // Gunakan find() bukan findOrFail() agar tidak melempar ModelNotFoundException
            $fakultas = Fakultas::find($id);

            if (!$fakultas) {
                $this->dispatch('toast', type: 'error', message: 'Gagal! Data tidak ditemukan atau sudah dihapus oleh pengguna lain.');
                $this->dispatch('pg:eventRefresh-fakultas-table');
                return;
            }

            // PAGAR PENGAMAN: Cek relasi Prodi
            if ($fakultas->prodis()->exists()) {
                $count = $fakultas->prodis()->count();
                throw new \Exception("Gagal! Fakultas {$fakultas->nama_fakultas} masih memiliki {$count} Program Studi aktif.");
            }

            DB::beginTransaction();

            $fakultas->delete();

            DB::commit();

            // Refresh tabel
            $this->dispatch('pg:eventRefresh-fakultas-table');

            $this->dispatch('toast', type: 'success', message: 'Fakultas berhasil dihapus.');
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            $this->dispatch('toast', type: 'error', message: $e->getMessage());
        }
    }

    /**
     * Listener untuk aksi edit
     */
    #[On('editFakultas')]
    public function edit($id): void
    {
        $this->dispatch('editFakultas', id: $id);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            // Menyamakan gaya Kode dengan Prodi (Monospace Badge)
            ->add('kode_html', function ($model) {
                return '<span class="font-mono text-[11px] font-black text-[#002855] bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">' . $model->kode_fakultas . '</span>';
            })
            // Menyamakan gaya Nama Fakultas (Bold + Sub-info Dekan)
            ->add('nama_fakultas_html', function ($model) {
                $html = '<div class="text-sm font-bold text-slate-800">' . $model->nama_fakultas . '</div>';
                $dekan = $model->dekan ?: '-';
                if ($dekan !== '-') {
                    $html .= '<div class="text-[10px] text-slate-500 mt-0.5 font-medium flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                Dekan: ' . $dekan . '
                              </div>';
                }
                return $html;
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Kode', 'kode_html', 'kode_fakultas')
                ->sortable()
                ->searchable(),

            Column::make('Fakultas', 'nama_fakultas_html', 'nama_fakultas')
                ->sortable()
                ->searchable(),

            Column::action('Aksi')
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left')
        ];
    }

    public function actions(Fakultas $row): array
    {
        $actions = [];

        // Proteksi Edit
        if (auth()->user()->can('edit_fakultas')) {
            $actions[] = Button::add('edit')
                ->slot('EDIT')
                ->class('text-[11px] font-black uppercase tracking-widest text-[#002855] hover:text-unmaris-gold transition-all border-0 bg-transparent cursor-pointer')
                ->dispatch('editFakultas', ['id' => $row->id]);
        }

        // Proteksi Delete dengan SweetAlert
        if (auth()->user()->can('delete_fakultas')) {
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
                    'name'  => $row->nama_fakultas,
                    'event' => 'deleteFakultas'
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
