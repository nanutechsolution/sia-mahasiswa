<div class="space-y-6">
    {{-- Info Kelas Card --}}
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-[#002855] text-[#fcc000] rounded-2xl flex items-center justify-center text-3xl font-black shadow-lg">
                    {{ substr($jadwal->mataKuliah->nama_mk, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-2xl font-black text-[#002855] uppercase tracking-tight">{{ $jadwal->mataKuliah->nama_mk }}</h1>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $jadwal->nama_kelas }}</span>
                        <span class="text-slate-300">|</span>
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ $jadwal->tahunAkademik->nama_tahun }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $isLocked ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }}">
                            {{ $isLocked ? 'Input Terkunci' : 'Input Terbuka' }}
                        </span>
                    </div>
                </div>
            </div>
            
            @if(!$isLocked)
            <button wire:click="publishAll" wire:confirm="Publikasikan semua nilai? Mahasiswa akan dapat melihat nilai mereka di KHS." class="px-8 py-3 bg-[#002855] text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-indigo-900/20 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                Publish Ke KHS
            </button>
            @endif
        </div>
    </div>

    @if (session()->has('global_success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-2xl text-emerald-800 text-sm font-bold flex items-center shadow-sm animate-in fade-in">
            <svg class="w-5 h-5 mr-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            {{ session('global_success') }}
        </div>
    @endif

    {{-- Tabel Input Dinamis --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/50">
                    <tr>
                        <th class="px-8 py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Mahasiswa</th>
                        
                        {{-- LOOP HEADER KOMPONEN (DINAMIS) --}}
                        @foreach($komponenBobot as $kb)
                            <th class="px-4 py-5 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] bg-indigo-50/30">
                                {{ $kb->nama_komponen }}
                                <div class="text-[8px] text-indigo-400 mt-1">Bobot: {{ number_format($kb->bobot_persen, 0) }}%</div>
                            </th>
                        @endforeach

                        <th class="px-4 py-5 text-center text-[10px] font-black text-[#002855] uppercase tracking-[0.2em] bg-slate-100">Nilai Akhir</th>
                        <th class="px-4 py-5 text-center text-[10px] font-black text-[#002855] uppercase tracking-[0.2em] bg-slate-100">Grade</th>
                        <th class="px-8 py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @foreach($pesertaKelas as $mhs)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-8 py-4">
                            <div class="text-sm font-black text-slate-800">{{ $mhs->krs->mahasiswa->person->nama_lengkap }}</div>
                            <div class="text-[10px] font-mono font-bold text-slate-400 mt-0.5 tracking-widest">{{ $mhs->krs->mahasiswa->nim }}</div>
                        </td>

                        {{-- LOOP INPUT NILAI (DINAMIS) --}}
                        @foreach($komponenBobot as $kb)
                            <td class="px-2 py-4 bg-indigo-50/10">
                                <input type="number" 
                                    wire:model.defer="inputNilai.{{ $mhs->id }}.{{ $kb->id }}"
                                    class="w-full text-center rounded-xl border-slate-200 bg-white text-xs font-black py-2 focus:ring-2 focus:ring-[#fcc000] outline-none {{ $mhs->is_published || $isLocked ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $mhs->is_published || $isLocked ? 'disabled' : '' }}
                                >
                            </td>
                        @endforeach

                        {{-- Hasil Kalkulasi Real-time dari Action --}}
                        <td class="px-4 py-4 text-center bg-slate-50 font-black text-slate-800">
                            {{ number_format($mhs->nilai_angka, 2) }}
                        </td>
                        <td class="px-4 py-4 text-center bg-slate-50">
                            <span class="text-lg font-black {{ $mhs->nilai_indeks >= 2 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $mhs->nilai_huruf ?? '-' }}
                            </span>
                        </td>

                        <td class="px-8 py-4 text-right">
                            @if(!$mhs->is_published && !$isLocked)
                                <div class="flex items-center justify-end gap-3">
                                    @if(session()->has('ok-'.$mhs->id))
                                        <span class="text-[9px] font-black text-emerald-500 uppercase animate-pulse">Berhasil</span>
                                    @endif
                                    <button wire:click="saveLine('{{ $mhs->id }}')" class="p-2 bg-indigo-600 text-white rounded-xl shadow-lg hover:scale-110 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                    </button>
                                </div>
                            @elseif($mhs->is_published)
                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-emerald-500 uppercase tracking-tighter">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                                    Terbit
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if(count($pesertaKelas) == 0)
            <div class="py-20 text-center flex flex-col items-center justify-center bg-slate-50/50">
                <div class="text-4xl mb-4">👥</div>
                <p class="text-slate-400 font-bold text-sm">Belum ada mahasiswa yang terdaftar atau KRS belum disetujui PA.</p>
            </div>
        @endif
    </div>
</div>