<div class="space-y-6">
    {{-- Header & Navigasi Tab --}}
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-black text-[#002855] uppercase tracking-tight">SDM & Pejabat Kampus</h1>
                <p class="text-sm text-slate-500 font-medium">Kelola data personil, gelar akademik, dan penugasan struktural.</p>
            </div>
            
            <div class="flex gap-2">
                @if(in_array($activeTab, ['personil', 'pegawai']) && !$showForm)
                    <button wire:click="openImport" class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                        <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        Import CSV
                    </button>
                @endif
                <button wire:click="$toggle('showForm')" class="bg-[#002855] text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:bg-[#001a38] transition-all">
                    {{ $showForm ? 'Tutup Form' : '+ Tambah Data' }}
                </button>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 p-1 bg-slate-100 rounded-2xl w-fit">
            @foreach(['personil' => 'Data Personil', 'pegawai' => 'Data Pegawai', 'role' => 'Master Role', 'jabatan' => 'Master Jabatan', 'gelar' => 'Master Gelar', 'penugasan' => 'Penugasan'] as $tab => $label)
                <button wire:click="switchTab('{{ $tab }}')" 
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $activeTab == $tab ? 'bg-white shadow-sm text-[#002855] border border-indigo-100' : 'text-slate-500 hover:text-slate-700' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-emerald-800 text-sm font-bold animate-in slide-in-from-top-2">
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-200 p-4 rounded-2xl text-rose-800 text-sm font-bold animate-in slide-in-from-top-2">
            {{ session('error') }}
        </div>
    @endif

    {{-- Import Modal --}}
    @if($showImportModal)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20">
            <div class="bg-[#002855] px-8 py-6 text-white flex justify-between items-center">
                <h3 class="text-lg font-black uppercase tracking-widest leading-none">Import {{ ucfirst($activeTab) }}</h3>
                <button wire:click="$set('showImportModal', false)" class="text-white/80 hover:text-white">&times;</button>
            </div>
            <div class="p-8 space-y-6">
                <div class="text-sm text-slate-600">
                    <p class="mb-2 font-bold">Instruksi:</p>
                    <ul class="list-disc pl-5 space-y-1 text-xs">
                        <li>Gunakan format CSV (.csv).</li>
                        <li>Baris pertama adalah header (akan dilewati).</li>
                        <li>Pastikan urutan kolom sesuai template.</li>
                    </ul>
                    <button wire:click="downloadTemplate" class="mt-3 text-indigo-600 font-bold text-xs hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Download Template CSV
                    </button>
                </div>
                
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <input type="file" wire:model="fileImport" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#002855] file:text-white hover:file:bg-[#001a38]">
                    @error('fileImport') <span class="text-rose-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button wire:click="processImport" wire:loading.attr="disabled" class="bg-emerald-600 text-white px-8 py-2 rounded-xl font-bold text-sm shadow-lg hover:bg-emerald-700 transition-all flex items-center">
                        <span wire:loading.remove>Mulai Import</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Form Section --}}
    @if($showForm)
    <div class="bg-white p-8 rounded-3xl shadow-xl border border-indigo-100 animate-in zoom-in-95 duration-200">
        <h3 class="text-lg font-black text-slate-800 uppercase mb-6 tracking-tight">{{ $editMode ? 'Edit' : 'Input' }} {{ ucfirst($activeTab) }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($activeTab == 'personil')
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nama Lengkap</label><input type="text" wire:model="nama_lengkap" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">NIK / Identitas</label><input type="text" wire:model="nik" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Email</label><input type="email" wire:model="email" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">No. HP</label><input type="text" wire:model="no_hp" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
            
            @elseif($activeTab == 'pegawai')
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pilih Personil</label>
                    <div x-data="{ open: false }" class="relative">
                        <input type="text" wire:model.live="searchPerson" @focus="open = true" @click.away="open = false" placeholder="{{ $selectedPersonName ?: '-- Cari Nama Personil --' }}" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]">
                        @if(!empty($searchPerson) && !empty($listPerson))
                            <div x-show="open" class="absolute z-10 w-full bg-white shadow-xl max-h-60 rounded-xl py-1 mt-1 overflow-auto border border-slate-100">
                                @foreach($listPerson as $p)
                                <div wire:click="pilihPerson('{{ $p->id }}', '{{ $p->nama_lengkap }}')" @click="open = false" class="px-4 py-2 hover:bg-indigo-50 cursor-pointer text-sm font-bold text-slate-700 border-b border-slate-50 last:border-0">{{ $p->nama_lengkap }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">NIP</label><input type="text" wire:model="nip" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Jenis Pegawai</label>
                    <select wire:model="jenis_pegawai" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]">
                        <option value="TENDIK">TENDIK</option><option value="ADMIN">ADMINISTRASI</option><option value="TEKNISI">TEKNISI</option><option value="LAINNYA">LAINNYA</option>
                    </select>
                </div>
                <div class="flex items-center pt-5"><label class="inline-flex items-center cursor-pointer"><input type="checkbox" wire:model="is_active_pegawai" class="rounded border-slate-300 text-[#002855] h-5 w-5"><span class="ml-2 text-sm font-bold text-slate-700">Status Aktif</span></label></div>

            @elseif($activeTab == 'role')
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Kode Role</label><input type="text" wire:model="kode_role_input" placeholder="DOSEN" class="w-full rounded-xl border-slate-200 text-sm uppercase py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nama Role</label><input type="text" wire:model="nama_role_input" placeholder="Tenaga Pengajar" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
            @elseif($activeTab == 'jabatan')
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Kode Jabatan</label><input type="text" wire:model="kode_jabatan" class="w-full rounded-xl border-slate-200 text-sm uppercase py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nama Jabatan</label><input type="text" wire:model="nama_jabatan_input" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
            @elseif($activeTab == 'gelar')
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Kode Gelar</label><input type="text" wire:model="kode_gelar" placeholder="M.T" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nama Gelar</label><input type="text" wire:model="nama_gelar_input" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Posisi</label><select wire:model="posisi_gelar" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"><option value="BELAKANG">BELAKANG</option><option value="DEPAN">DEPAN</option></select></div>
            
            @elseif($activeTab == 'penugasan')
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pilih Personil</label>
                        <div x-data="{ open: false }" class="relative">
                            <input type="text" wire:model.live="searchPerson" @focus="open = true" @click.away="open = false" placeholder="{{ $selectedPersonName ?: '-- Cari Nama Personil --' }}" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]">
                            @if(!empty($searchPerson) && !empty($listPerson))
                                <div x-show="open" class="absolute z-10 w-full bg-white shadow-xl max-h-60 rounded-xl py-1 mt-1 overflow-auto border border-slate-100">
                                    @foreach($listPerson as $p)
                                    <div wire:click="pilihPerson('{{ $p->id }}', '{{ $p->nama_lengkap }}')" @click="open = false" class="px-4 py-2 hover:bg-indigo-50 cursor-pointer text-sm font-bold text-slate-700 border-b border-slate-50 last:border-0">{{ $p->nama_lengkap }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pilih Jabatan</label>
                        <select wire:model="target_jabatan_id" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]">
                            <option value="">-- Pilih Jabatan --</option>
                            @foreach($listJabatan as $j)<option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>@endforeach
                        </select>
                    </div>
                </div>
                
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Unit Prodi (Opsional)</label>
                        <select wire:model="prodi_id" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($listProdi as $pr)<option value="{{ $pr->id }}">{{ $pr->nama_prodi }}</option>@endforeach
                        </select>
                        <p class="text-[9px] text-slate-400 mt-1">*Isi jika jabatan spesifik untuk Prodi tertentu (e.g. Kaprodi)</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Unit Fakultas (Opsional)</label>
                        <select wire:model="fakultas_id" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]">
                            <option value="">-- Tidak Ada --</option>
                            @foreach($listFakultas as $f)<option value="{{ $f->id }}">{{ $f->nama_fakultas }}</option>@endforeach
                        </select>
                        <p class="text-[9px] text-slate-400 mt-1">*Isi jika jabatan spesifik untuk Fakultas (e.g. Dekan)</p>
                    </div>
                </div>

                <div class="md:col-span-2 grid grid-cols-2 gap-6">
                    <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Mulai Menjabat</label><input type="date" wire:model="tanggal_mulai" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                    <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Selesai Menjabat</label><input type="date" wire:model="tanggal_selesai" class="w-full rounded-xl border-slate-200 text-sm py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                </div>
            @endif
        </div>
        <div class="mt-8 flex justify-end gap-3 border-t pt-6">
            <button wire:click="resetForm" class="px-6 py-2 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Batal</button>
            <button wire:click="save{{ ucfirst($activeTab) }}" class="bg-[#002855] text-white px-10 py-2 rounded-xl font-bold text-sm shadow-md hover:bg-[#001a38] transition-all">Simpan Data</button>
        </div>
    </div>
    @endif

    {{-- Tabel Data --}}
    <div class="bg-white shadow-sm rounded-3xl border border-slate-200 overflow-hidden">
        <div class="p-4 bg-slate-50/50 border-b flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari data..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 text-sm focus:ring-[#002855] py-2 focus:outline-none focus:ring-2 focus:ring-[#fcc000]">
                <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        @if($activeTab == 'personil')
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama / Identitas</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Role Aktif</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        @elseif($activeTab == 'pegawai')
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Pegawai</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">NIP / Jenis</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        @elseif($activeTab == 'role')
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Keterangan</th>
                            <th class="px-6 py-4 text-right"></th>
                        @elseif($activeTab == 'jabatan')
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Kode</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nama Jabatan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        @elseif($activeTab == 'gelar')
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Gelar</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Posisi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        @elseif($activeTab == 'penugasan')
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Pejabat</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Jabatan</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Unit Kerja</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Masa Berlaku</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($listData as $item)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            @if($activeTab == 'personil')
                                <td class="px-6 py-4"><div class="text-sm font-black text-slate-800">{{ $item->nama_lengkap }}</div><div class="text-[10px] font-mono text-slate-400">NIK: {{ $item->nik ?? '-' }}</div></td>
                                <td class="px-6 py-4">@php $roles = DB::table('trx_person_role as tr')->join('ref_person_role as rr', 'tr.role_id', '=', 'rr.id')->where('tr.person_id', $item->id)->pluck('rr.kode_role'); @endphp<div class="flex flex-wrap gap-1">@foreach($roles as $r)<span class="bg-indigo-50 text-indigo-600 text-[9px] font-black px-1.5 py-0.5 rounded border border-indigo-100 uppercase">{{ $r }}</span>@endforeach</div></td>
                            @elseif($activeTab == 'pegawai')
                                <td class="px-6 py-4"><div class="text-sm font-black text-slate-800">{{ $item->nama_lengkap }}</div><div class="text-[10px] font-mono text-slate-400">ID Person: {{ $item->person_id }}</div></td>
                                <td class="px-6 py-4"><div class="text-sm font-bold text-slate-700">{{ $item->nip ?? '-' }}</div><div class="text-[10px] font-bold text-indigo-500 uppercase">{{ $item->jenis_pegawai }}</div></td>
                                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $item->is_active ? 'Aktif' : 'Non-Aktif' }}</span></td>
                            @elseif($activeTab == 'role')
                                <td class="px-6 py-4 font-black text-indigo-600 uppercase">{{ $item->kode_role }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-slate-700">{{ $item->nama_role }}</td>
                            @elseif($activeTab == 'jabatan')
                                <td class="px-6 py-4 font-black text-indigo-600 text-sm uppercase">{{ $item->kode_jabatan }}</td>
                                <td class="px-6 py-4 font-bold text-slate-700 text-sm">{{ $item->nama_jabatan }}</td>
                            @elseif($activeTab == 'gelar')
                                <td class="px-6 py-4 text-sm font-black text-slate-800">{{ $item->kode }} <span class="text-[10px] font-normal text-slate-400">({{ $item->nama }})</span></td>
                                <td class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase">{{ $item->posisi }}</td>
                            @elseif($activeTab == 'penugasan')
                                <td class="px-6 py-4"><div class="text-sm font-black text-slate-800">{{ $item->nama_lengkap }}</div></td>
                                <td class="px-6 py-4 font-bold text-indigo-600 text-sm">{{ $item->nama_jabatan }}</td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">
                                    @if($item->nama_prodi)
                                        Prodi {{ $item->nama_prodi }}
                                    @elseif($item->nama_fakultas)
                                        Fakultas {{ $item->nama_fakultas }}
                                    @else
                                        Universitas
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-[11px] font-mono text-slate-400">
                                    {{ date('d/m/y', strtotime($item->tanggal_mulai)) }} - {{ $item->tanggal_selesai ? date('d/m/y', strtotime($item->tanggal_selesai)) : 'Sekarang' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $expired = $item->tanggal_selesai && $item->tanggal_selesai < date('Y-m-d'); @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $expired ? 'bg-rose-100 text-rose-600' : 'bg-green-100 text-green-600' }}">
                                        {{ $expired ? 'Berakhir' : 'Aktif' }}
                                    </span>
                                </td>
                            @endif

                            <td class="px-6 py-4 text-right space-x-2">
                                @if($activeTab == 'personil')
                                    <button wire:click="editPerson({{ $item->id }})" class="text-indigo-600 font-black text-[10px] uppercase">Edit</button>
                                    <button wire:click="openRoleModal({{ $item->id }})" class="text-emerald-600 font-black text-[10px] uppercase">Role</button>
                                    <button wire:click="openDegreeModal({{ $item->id }})" class="text-amber-600 font-black text-[10px] uppercase">Gelar</button>
                                @elseif($activeTab == 'pegawai')
                                    <button wire:click="editPegawai({{ $item->id }})" class="text-indigo-600 font-black text-[10px] uppercase">Edit</button>
                                @elseif($activeTab == 'role')
                                    <button wire:click="editRole({{ $item->id }})" class="text-indigo-600 font-black text-[10px] uppercase">Edit</button>
                                @elseif($activeTab == 'jabatan')
                                    <button wire:click="editJabatan({{ $item->id }})" class="text-indigo-600 font-black text-[10px] uppercase">Edit</button>
                                @elseif($activeTab == 'gelar')
                                    <button wire:click="editGelar({{ $item->id }})" class="text-indigo-600 font-black text-[10px] uppercase">Edit</button>
                                @elseif($activeTab == 'penugasan')
                                    <button wire:click="editPenugasan({{ $item->id }})" class="text-indigo-600 font-black text-[10px] uppercase">Edit</button>
                                @endif
                                <button wire:click="deleteData({{ $item->id }})" wire:confirm="Hapus data?" class="text-rose-500 font-black text-[10px] uppercase">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400 text-sm italic">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50/50">{{ $listData->links() }}</div>
    </div>

    {{-- MODAL ATUR ROLE PERSONIL (FIXED FIND ISSUE) --}}
    @if($assign_person_id && $activeModal == 'role')
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20">
            <div class="bg-indigo-600 px-8 py-6 text-white">
                <h3 class="text-lg font-black uppercase tracking-widest leading-none">Penetapan Role</h3>
                {{-- FIX: Gunakan firstWhere untuk Collection --}}
                <p class="text-[10px] font-bold uppercase opacity-60 mt-2">Personil: {{ $listPerson->firstWhere('id', $assign_person_id)->nama_lengkap ?? '-' }}</p>
            </div>
            <div class="p-8 space-y-6">
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pilih Role</label><select wire:model="assign_role_id" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"><option value="">-- Pilih --</option>@foreach($listRoles as $r)<option value="{{ $r->id }}">{{ $r->nama_role }}</option>@endforeach</select></div>
                        <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Mulai</label><input type="date" wire:model="assign_tgl_mulai_role" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                    </div>
                    <button wire:click="saveAssignmentRole" class="w-full mt-4 bg-indigo-600 text-white py-2 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg">Tetapkan Role</button>
                </div>
                <div class="space-y-3">
                    @php $assignedRoles = DB::table('trx_person_role as tr')->join('ref_person_role as rr', 'tr.role_id', '=', 'rr.id')->where('tr.person_id', $assign_person_id)->select('tr.id', 'rr.nama_role', 'tr.tanggal_mulai')->get(); @endphp
                    @forelse($assignedRoles as $ar)
                        <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-2xl">
                            <div><p class="text-sm font-black text-slate-700">{{ $ar->nama_role }}</p><p class="text-[9px] font-bold text-slate-400">Sejak: {{ $ar->tanggal_mulai }}</p></div>
                            <button wire:click="removeAssignmentRole({{ $ar->id }})" class="p-2 text-rose-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                        </div>
                    @empty <p class="text-center text-xs text-slate-400 italic">Belum ada role ditetapkan.</p> @endforelse
                </div>
            </div>
            <div class="p-8 border-t border-slate-50 bg-slate-50/50 text-right"><button wire:click="closeModal" class="text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">Selesai & Tutup</button></div>
        </div>
    </div>
    @endif

    {{-- MODAL ATUR GELAR PERSONIL (ADDED) --}}
    @if($assign_person_id && $activeModal == 'gelar')
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden border border-white/20">
            <div class="bg-amber-500 px-8 py-6 text-white">
                <h3 class="text-lg font-black uppercase tracking-widest leading-none">Manajemen Gelar</h3>
                <p class="text-[10px] font-bold uppercase opacity-80 mt-2">Personil: {{ $listPerson->firstWhere('id', $assign_person_id)->nama_lengkap ?? '-' }}</p>
            </div>
            <div class="p-8 space-y-6">
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2"><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Pilih Gelar</label><select wire:model="assign_gelar_id" class="w-full rounded-xl border-slate-200 text-sm font-bold py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"><option value="">-- Pilih --</option>@foreach($listGelar as $g)<option value="{{ $g->id }}">{{ $g->kode }} ({{ $g->nama }})</option>@endforeach</select></div>
                        <div><label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Urutan</label><input type="number" wire:model="assign_urutan" class="w-full rounded-xl border-slate-200 text-sm text-center font-bold py-2 pl-4 focus:outline-none focus:ring-2 focus:ring-[#fcc000]"></div>
                    </div>
                    <button wire:click="saveAssignmentGelar" class="w-full mt-4 bg-amber-500 text-white py-2 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg hover:bg-amber-600 transition-all">Hubungkan Gelar</button>
                </div>
                <div class="space-y-3">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Gelar Terdaftar</h4>
                    @php $assignedGelars = DB::table('trx_person_gelar as tpg')->join('ref_gelar as rg', 'tpg.gelar_id', '=', 'rg.id')->where('tpg.person_id', $assign_person_id)->select('tpg.id', 'rg.kode', 'tpg.urutan', 'rg.posisi')->orderBy('tpg.urutan', 'asc')->get(); @endphp
                    @forelse($assignedGelars as $ag)
                        <div class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-2xl">
                            <div class="flex items-center gap-3"><span class="w-6 h-6 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black">{{ $ag->urutan }}</span><div><p class="text-sm font-black text-slate-700">{{ $ag->kode }}</p><p class="text-[9px] font-bold text-slate-400 uppercase">{{ $ag->posisi }}</p></div></div>
                            <button wire:click="removeAssignmentGelar({{ $ag->id }})" class="p-2 text-rose-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                        </div>
                    @empty <p class="text-center text-xs text-slate-400 italic py-4">Belum ada gelar terdaftar.</p> @endforelse
                </div>
            </div>
            <div class="p-8 border-t border-slate-50 bg-slate-50/50 text-right"><button wire:click="closeModal" class="text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">Selesai & Tutup</button></div>
        </div>
    </div>
    @endif
</div>