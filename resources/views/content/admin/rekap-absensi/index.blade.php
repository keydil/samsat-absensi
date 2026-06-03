@extends('layouts.app')

@section('content')
    <div class="space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekap Absensi</h1>
                <p class="text-sm text-slate-500">Pantau kehadiran pegawai secara realtime.</p>
            </div>

            <a href="{{ route('admin.rekap-absensi.export', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export Excel
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">

            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <form action="{{ route('admin.rekap-absensi') }}" method="GET" class="flex gap-4">
                    <input type="date" name="tanggal"
                        class="rounded-lg border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                        value="{{ request('tanggal') ?? date('Y-m-d') }}">
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                        Terapkan Filter
                    </button>

                    @if (request('tanggal'))
                        <a href="{{ route('admin.rekap-absensi') }}"
                            class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 transition-colors">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Pegawai</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Jam Pulang</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($absensi as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if ($item->present_user_image)
                                            <img src="{{ $item->present_user_image }}"
                                                alt="Foto {{ $item->user->name }}"
                                                class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-200 cursor-pointer hover:ring-blue-400 transition"
                                                onclick="window.open('{{ $item->present_user_image }}', '_blank')"
                                                title="Klik untuk perbesar">
                                        @else
                                            <div
                                                class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                {{ substr($item->user->name ?? 'U', 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                {{ $item->user->name ?? 'User Terhapus' }}</p>
                                            <p class="text-xs text-slate-400">{{ $item->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 font-medium text-slate-700">
                                    {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}
                                </td>

                                <td class="px-6 py-4 font-mono text-xs">
                                    @if ($item->jam_masuk)
                                        <span class="text-emerald-600 font-bold">
                                            {{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 font-mono text-xs">
                                    @if ($item->jam_pulang)
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
                                        
                                        @if($item->bukti_surat)
                                            <a href="{{ $item->bukti_surat }}" target="_blank" class="inline-flex items-center gap-1 rounded bg-white px-2 py-1 text-[10px] font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition">
                                                📄 Lihat Surat
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <p>Belum ada data absensi yang masuk.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $absensi->links() }}
            </div>
        </div>
    </div>
@endsection