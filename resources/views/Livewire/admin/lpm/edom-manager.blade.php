<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#002855]">Evaluasi Dosen (EDOM)</h2>
            <p class="text-sm text-slate-500">Analisis kinerja pengajaran berdasarkan persepsi mahasiswa.</p>
        </div>
        <button class="bg-[#002855] text-white px-5 py-2.5 rounded-xl font-bold text-sm">Download Rekap Univ</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100"><p class="text-[10px] font-black text-slate-400 uppercase mb-2">Total Responden</p><h3 class="text-3xl font-black text-[#002855]">{{ $stats['total_responden'] }}</h3><p class="text-[10px] text-indigo-600 font-bold mt-2">Partisipasi: {{ $stats['partisipasi_mhs'] }}%</p></div>
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100"><p class="text-[10px] font-black text-slate-400 uppercase mb-2">Skor Rata-rata Univ</p><h3 class="text-3xl font-black text-emerald-600">{{ number_format($stats['rata_rata_univ'], 2) }} <span class="text-xs text-slate-400">/4.0</span></h3><p class="text-[10px] text-slate-500 font-bold mt-2">Kategori: SANGAT BAIK</p></div>
        <div class="bg-indigo-900 p-6 rounded-3xl shadow-lg text-white relative overflow-hidden"><div class="absolute right-0 top-0 p-4 opacity-10"><svg class="w-20 h-20" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2h12a2 2 0 012 2v11a2 2 0 01-2 2H4a2 2 0 01-2-2V5z" clip-rule="evenodd"/></svg></div><p class="text-[10px] font-black text-[#fcc000] uppercase mb-2">Evaluasi Semester Ini</p><h3 class="text-xl font-black">Genap 2025/2026</h3><button class="mt-4 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Atur Kuisioner</button></div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50"><h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">Dosen dengan Kinerja Tertinggi</h3></div>
        <div class="divide-y divide-slate-100">
            @foreach($stats['top_performers'] as $d)
            <div class="px-8 py-5 flex items-center justify-between group hover:bg-slate-50 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-[#002855] rounded-xl flex items-center justify-center text-white font-black text-sm">{{ substr($d['nama'], 0, 1) }}</div>
                    <div><p class="text-sm font-black text-slate-800">{{ $d['nama'] }}</p><p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Prodi {{ $d['prodi'] }}</p></div>
                </div>
                <div class="text-right">
                    <span class="text-lg font-black text-emerald-600">{{ $d['skor'] }}</span>
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Skor Indeks</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>