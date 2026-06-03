@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Riwayat Saya</h1>
                <p class="text-sm text-slate-500">Daftar kehadiran absensi anda.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                    Total Bulan Ini: {{ \App\Models\Absen::where('user_id', Auth::id())->whereMonth('created_at', date('m'))->count() }} Aktivitas
                </span>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <form action="{{ route('user.history') }}" method="GET" class="flex gap-4">
                    <input 
                        type="date" 
                        name="tanggal" 
                        class="rounded-lg border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500" 
                        value="{{ request('tanggal') }}"
                    >
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                        Cari
                    </button>
                    @if(request('tanggal'))
                        <a href="{{ route('user.history') }}" class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 transition-colors">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="p-4 sm:p-6 bg-slate-50">
                <div class="space-y-4">
                    @forelse($riwayat as $item)
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                            
                            {{-- Header Card --}}
                            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-bold text-slate-700 text-sm">
                                        {{ \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y') }}
                                    </span>
                                </div>
                                
                                {{-- Main Status Badge --}}
                                <div>
                                    @if($item->status === 'Hadir')
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700">Hadir</span>
                                    @elseif($item->status === 'Telat')
                                        <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-orange-700">Telat</span>
                                    @elseif($item->status === 'Izin')
                                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-700">Izin</span>
                                    @elseif($item->status === 'Sakit')
                                        <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-rose-700">Sakit</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-700">{{ $item->status }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Body Card --}}
                            <div class="px-4 py-4">
                                @if(in_array($item->status, ['Izin', 'Sakit']))
                                    {{-- Tampilan untuk Izin/Sakit --}}
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 shrink-0 rounded-full bg-blue-50 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">Pengajuan {{ $item->status }}</p>
                                            
                                            {{-- Approval Status --}}
                                            <div class="mt-2">
                                                @if(isset($item->approval_status))
                                                    @if($item->approval_status == 'pending')
                                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-md ring-1 ring-inset ring-amber-500/20">
                                                            <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                                            Menunggu Persetujuan HRD
                                                        </span>
                                                    @elseif($item->approval_status == 'approved')
                                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md ring-1 ring-inset ring-emerald-500/20">
                                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                            Disetujui
                                                        </span>
                                                    @elseif($item->approval_status == 'rejected')
                                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-rose-600 bg-rose-50 px-2 py-1 rounded-md ring-1 ring-inset ring-rose-500/20">
                                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                            Ditolak
                                                        </span>
                                                    @endif
                                                @else
                                                    {{-- Fallback untuk data lama --}}
                                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md ring-1 ring-inset ring-emerald-500/20">
                                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        Disetujui (Auto)
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Tampilan untuk Hadir/Telat (Absen Fisik) --}}
                                    <div class="grid grid-cols-2 gap-4">
                                        {{-- Jam Masuk --}}
                                        <div class="flex flex-col border-r border-slate-100 pr-4">
                                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">In (Masuk)</span>
                                            <div class="flex items-center gap-2">
                                                <div class="h-8 w-8 shrink-0 rounded-full {{ $item->jam_masuk ? 'bg-emerald-100' : 'bg-slate-100' }} flex items-center justify-center">
                                                    <svg class="h-4 w-4 {{ $item->jam_masuk ? 'text-emerald-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                    </svg>
                                                </div>
                                                @if($item->jam_masuk)
                                                    <span class="text-xl font-black tabular-nums tracking-tight text-slate-800">{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</span>
                                                @else
                                                    <span class="text-sm font-medium text-slate-400">Belum Ada</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Jam Pulang --}}
                                        <div class="flex flex-col pl-2">
                                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Out (Pulang)</span>
                                            <div class="flex items-center gap-2">
                                                <div class="h-8 w-8 shrink-0 rounded-full {{ $item->jam_pulang ? 'bg-indigo-100' : 'bg-slate-100' }} flex items-center justify-center">
                                                    <svg class="h-4 w-4 {{ $item->jam_pulang ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                    </svg>
                                                </div>
                                                @if($item->jam_pulang)
                                                    <span class="text-xl font-black tabular-nums tracking-tight text-slate-800">{{ \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') }}</span>
                                                @else
                                                    <span class="text-sm font-medium text-slate-400">Belum Ada</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 px-4 rounded-2xl border border-dashed border-slate-300 bg-white">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-slate-500 font-medium">Belum ada riwayat absensi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="p-4 border-t border-slate-100">
                {{ $riwayat->links() }}
            </div>
        </div>
    </div>
@endsection