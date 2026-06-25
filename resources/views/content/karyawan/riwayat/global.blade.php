@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Absensi Global</h1>
                <p class="text-sm text-slate-500">Lihat status kehadiran rekan kerja Anda hari ini.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-700/10">
                    Hanya menampilkan data umum demi menjaga privasi.
                </span>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex flex-col sm:flex-row gap-4 items-end justify-between">
                <form action="{{ route('user.globalHistory') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end flex-1">
                    <div class="w-full sm:w-auto">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Pilih Tanggal</label>
                        <input 
                            type="date" 
                            name="tanggal" 
                            class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500" 
                            value="{{ $tanggalFilter }}"
                        >
                    </div>

                    <div class="flex gap-2 w-full sm:w-auto">
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors w-full sm:w-auto">
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>

            <div class="p-4 sm:p-6 bg-slate-50">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($riwayat as $item)
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 flex items-center justify-between hover:shadow-md transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-500">
                                    {{ substr($item->user->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ $item->user->name ?? 'User Terhapus' }}</p>
                                </div>
                            </div>
                            
                            <div>
                                @if($item->status === 'Hadir')
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-bold text-emerald-700">Hadir</span>
                                @elseif($item->status === 'Telat')
                                    <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-bold text-orange-700">Telat</span>
                                @elseif($item->status === 'Izin')
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-bold text-blue-700">Izin</span>
                                @elseif($item->status === 'Sakit')
                                    <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-700">Sakit</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-700">{{ $item->status }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12 px-4 rounded-2xl border border-dashed border-slate-300 bg-white">
                            <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="text-slate-500 font-medium">Belum ada data absensi untuk tanggal ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
        </div>
    </div>
@endsection
