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

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Jam Pulang</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($riwayat as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}
                                </td>

                                <td class="px-6 py-4 font-mono text-xs">
                                    @if($item->jam_masuk)
                                        <span class="text-emerald-600 font-bold">
                                            {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 font-mono text-xs">
                                    @if($item->jam_pulang)
                                        <span class="text-blue-600 font-bold">
                                            {{ \Carbon\Carbon::parse($item->jam_pulang)->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        @if($item->status === 'Hadir')
                                            <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Hadir</span>
                                        @elseif($item->status === 'Telat')
                                            <span class="inline-flex items-center rounded-full bg-orange-50 px-2 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20">Telat</span>
                                        @elseif($item->status === 'Izin')
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">Izin</span>
                                        @elseif($item->status === 'Sakit')
                                            <span class="inline-flex items-center rounded-full bg-rose-50 px-2 py-1 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20">Sakit</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-slate-50 px-2 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-600/20">{{ $item->status }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <p>Belum ada riwayat absensi.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-slate-100">
                {{ $riwayat->links() }}
            </div>
        </div>
    </div>
@endsection