<div>
{{-- SEO & Header --}}
<x-slot name="title">Audit Log System</x-slot>
<x-slot name="header">Audit Log & Keamanan Sistem</x-slot>

<div class="space-y-8">
    {{-- Top Toolbar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <p class="text-slate-500 text-sm">Rekam jejak aktivitas pengguna dan perubahan data kritis untuk integritas sistem.</p>
        </div>
        
        <div class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
            <span>System Monitoring Active</span>
        </div>
    </div>

    {{-- Log Table Section --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Waktu & Timestamp</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">User (Aktor)</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Aktivitas & Objek</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Detail Perubahan Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        {{-- Waktu --}}
                        <td class="px-8 py-6 whitespace-nowrap">
                            <div class="text-[13px] font-bold text-slate-700">{{ $log->created_at->format('d M Y') }}</div>
                            <div class="text-[11px] font-mono text-slate-400 mt-1">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>

                        {{-- Pelaku --}}
                        <td class="px-8 py-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-unmaris-blue text-unmaris-yellow rounded-xl flex items-center justify-center font-black text-xs shadow-sm">
                                    {{ substr($log->causer->name ?? 'S', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-[13px] font-bold text-slate-800 leading-tight">{{ $log->causer->name ?? 'System Process' }}</div>
                                    <div class="text-[10px] font-bold text-unmaris-gold uppercase tracking-tighter mt-0.5">{{ $log->causer->role ?? 'Internal' }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Aktivitas --}}
                        <td class="px-8 py-6">
                            <div class="flex flex-col space-y-1.5">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest border
                                        {{ $log->event == 'created' ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 
                                           ($log->event == 'updated' ? 'bg-blue-50 text-blue-600 border-blue-100' : 
                                           'bg-rose-50 text-rose-600 border-rose-100') }}">
                                        {{ $log->event }}
                                    </span>
                                    <span class="text-[12px] font-bold text-slate-700">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 font-medium italic">{{ $log->log_name }}</div>
                            </div>
                        </td>

                        {{-- Perubahan Data --}}
                        <td class="px-8 py-6">
                            <div class="max-w-md bg-slate-50 border border-slate-100 rounded-2xl p-4 overflow-hidden">
                                @if($log->event == 'updated')
                                    <div class="space-y-2">
                                        @foreach($log->properties['attributes'] ?? [] as $key => $val)
                                            @if(isset($log->properties['old'][$key]) && $log->properties['old'][$key] != $val)
                                                <div class="text-[11px] leading-relaxed">
                                                    <span class="font-black text-slate-400 uppercase mr-1">{{ $key }}:</span>
                                                    <span class="text-rose-400 line-through decoration-rose-300/50">{{ $log->properties['old'][$key] }}</span>
                                                    <span class="mx-1 text-slate-300">→</span>
                                                    <span class="text-emerald-600 font-bold bg-emerald-50 px-1 rounded">{{ $val }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif($log->event == 'created')
                                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest flex items-center">
                                        <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                        Entri Data Baru Berhasil
                                    </span>
                                @else
                                    <span class="text-[10px] font-black text-rose-400 uppercase tracking-widest italic">Data Telah Dihapus dari Database</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>


</div>