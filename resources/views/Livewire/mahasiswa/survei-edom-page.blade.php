<div class="max-w-4xl mx-auto pb-24 space-y-6 animate-in fade-in duration-700">
    
    {{-- Progress Tracker (Sticky on top) --}}
    @php
        $totalQuestions = 0;
        foreach($groups as $g) { $totalQuestions += count($g->questions); }
        $answeredCount = count($answers);
        $progress = $totalQuestions > 0 ? ($answeredCount / $totalQuestions) * 100 : 0;
    @endphp
    
    <div class="sticky top-0 z-40 bg-slate-100/80 backdrop-blur-md pt-2 pb-4 -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-3 flex items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex justify-between text-[10px] font-black text-[#002855] uppercase mb-1">
                    <span>Progres Pengisian</span>
                    <span>{{ $answeredCount }} / {{ $totalQuestions }} Pertanyaan</span>
                </div>
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 transition-all duration-500 shadow-sm" style="width: {{ $progress }}%"></div>
                </div>
            </div>
            <div class="hidden sm:block">
                <span class="text-lg font-black text-emerald-600">{{ round($progress) }}%</span>
            </div>
        </div>
    </div>

    {{-- Header Informasi Mata Kuliah --}}
    <div class="bg-white rounded-[2rem] shadow-xl border border-slate-200 overflow-hidden relative">
        <div class="absolute top-0 right-0 p-6 opacity-5 pointer-events-none">
            <svg class="w-32 h-32 text-[#002855]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
        </div>
        
        <div class="p-6 sm:p-8 relative z-10">
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-6">
                <div class="w-20 h-20 bg-[#002855] text-[#fcc000] rounded-3xl flex items-center justify-center font-black text-4xl shadow-2xl ring-4 ring-slate-50 shrink-0">
                    {{ substr($detail->jadwalKuliah->mataKuliah->nama_mk, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Evaluasi Pengajaran Dosen</p>
                    <h1 class="text-xl sm:text-2xl font-black text-[#002855] leading-tight uppercase truncate">
                        {{ $detail->jadwalKuliah->mataKuliah->nama_mk }}
                    </h1>
                    <div class="flex flex-wrap justify-center sm:justify-start items-center gap-2 mt-2">
                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                            {{ $detail->jadwalKuliah->dosen->person->nama_dengan_gelar ?? $detail->jadwalKuliah->dosen->person->nama_lengkap }}
                        </span>
                        <span class="text-xs font-mono font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                            KELAS {{ $detail->jadwalKuliah->nama_kelas }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 flex items-start gap-3">
        <div class="bg-indigo-100 p-1.5 rounded-lg text-indigo-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-[11px] text-indigo-800 leading-relaxed font-medium">
            Berikan penilaian Anda secara objektif sesuai pengalaman selama perkuliahan. Identitas Anda dirahasiakan sepenuhnya. <span class="font-black text-[#002855]">Semua pertanyaan wajib diisi.</span>
        </p>
    </div>

    {{-- Form Kuesioner --}}
    <form wire:submit.prevent="submit" class="space-y-8">
        @foreach($groups as $group)
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden" wire:key="group-{{ $group->id }}">
            <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xs font-black text-[#002855] uppercase tracking-widest">{{ $group->nama_kelompok }}</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ count($group->questions) }} Butir</span>
            </div>
            
            <div class="divide-y divide-slate-100">
                @foreach($group->questions as $index => $q)
                @php $isAnswered = isset($answers[$q->id]); @endphp
                <div class="p-6 sm:p-8 space-y-6 transition-colors duration-300 {{ $isAnswered ? 'bg-emerald-50/20' : '' }}" wire:key="q-{{ $q->id }}">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="flex items-center justify-between sm:block shrink-0">
                            <span class="flex-shrink-0 w-8 h-8 rounded-full {{ $isAnswered ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center text-xs font-black transition-all">
                                {{ $index + 1 }}
                            </span>
                            <span class="sm:hidden px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $isAnswered ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-50 text-rose-500' }}">
                                {{ $isAnswered ? 'Terisi' : 'Wajib' }}
                            </span>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <div class="hidden sm:flex items-center gap-2 mb-2">
                                <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ $isAnswered ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-50 text-rose-500' }}">
                                    {{ $isAnswered ? 'Sudah Terisi' : 'Wajib Diisi' }}
                                </span>
                            </div>
                            <p class="text-sm font-bold text-slate-700 leading-relaxed">
                                {{ $q->bunyi_pertanyaan }}
                            </p>
                        </div>
                    </div>

                    {{-- Skala Penilaian: Mobile First (2x2 Grid) --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:pl-12">
                        @php
                            $labels = [
                                1 => ['label' => 'Sangat Kurang', 'emoji' => '😡'],
                                2 => ['label' => 'Kurang', 'emoji' => '😐'],
                                3 => ['label' => 'Baik', 'emoji' => '😊'],
                                4 => ['label' => 'Sangat Baik', 'emoji' => '😍']
                            ];
                        @endphp
                        
                        @foreach($labels as $val => $data)
                        <label for="q{{ $q->id }}_{{ $val }}" 
                            class="relative flex flex-col items-center p-3 sm:p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 group
                            {{ ($answers[$q->id] ?? null) == $val ? 'bg-white border-[#002855] ring-4 ring-indigo-50 shadow-md' : 'bg-white border-slate-100 hover:border-slate-300' }}">
                            
                            <input type="radio" 
                                id="q{{ $q->id }}_{{ $val }}" 
                                wire:model.live="answers.{{ $q->id }}" 
                                value="{{ $val }}" 
                                class="sr-only">
                            
                            <div class="text-2xl mb-1 transition-transform group-active:scale-90 group-hover:scale-110">
                                {{ $data['emoji'] }}
                            </div>
                            
                            <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-tighter sm:tracking-widest text-center {{ ($answers[$q->id] ?? null) == $val ? 'text-[#002855]' : 'text-slate-400' }}">
                                {{ $data['label'] }}
                            </span>
                            
                            {{-- Checkmark Icon --}}
                            @if(($answers[$q->id] ?? null) == $val)
                            <div class="absolute -top-1.5 -right-1.5 bg-[#002855] text-white rounded-full p-1 shadow-lg animate-in zoom-in">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- Validation Error Alert --}}
        @if (session()->has('error') || $errors->any())
            <div class="p-4 bg-rose-50 border-2 border-rose-100 rounded-3xl animate-in shake duration-500">
                <div class="flex items-center gap-3 text-rose-700">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    <p class="text-sm font-black uppercase tracking-tight">Mohon lengkapi kuesioner!</p>
                </div>
                <p class="text-xs text-rose-500 mt-1 ml-8">Masih ada pertanyaan yang terlewatkan. KHS hanya dapat dibuka jika evaluasi selesai 100%.</p>
            </div>
        @endif

        {{-- Sticky Footer Action --}}
        <div class="bg-white/90 backdrop-blur-md p-5 rounded-[2rem] shadow-2xl border border-slate-200 fixed bottom-4 left-4 right-4 sm:left-auto sm:right-auto sm:max-w-4xl sm:w-full sm:relative sm:bottom-0 z-50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="hidden md:block">
                <h4 class="text-sm font-black text-[#002855] uppercase tracking-tight">Selesai Berbagi?</h4>
                <p class="text-[10px] text-slate-400 font-bold">Terima kasih atas kontribusi Anda untuk kampus.</p>
            </div>
            
            <div class="flex items-center justify-between w-full sm:w-auto gap-4">
                <a href="{{ route('mhs.khs') }}" class="px-6 py-3 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">Batal</a>
                
                <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                    @if($progress < 100) disabled @endif
                    class="flex-1 sm:flex-none px-10 py-4 bg-[#002855] text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-indigo-900/20 hover:bg-black hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3 disabled:opacity-30 disabled:grayscale disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="submit">
                        {{ $progress < 100 ? 'Lengkapi Data' : 'Kirim Jawaban' }}
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Sabar...
                    </span>
                </button>
            </div>
        </div>
    </form>

    {{-- Bottom Padding for Sticky Mobile Footer --}}
    <div class="h-20 sm:hidden"></div>
</div>