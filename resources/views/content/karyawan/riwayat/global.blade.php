@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Absensi Global</h1>
                <p class="text-sm text-slate-500">Lihat rekapitulasi kehadiran rekan kerja Anda.</p>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-700/10">
                    Menampilkan total rekapan kehadiran per periode.
                </span>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4 flex flex-col sm:flex-row gap-4 items-end justify-between">
                <form action="{{ route('user.globalHistory') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end flex-1">
                    <div class="w-full sm:w-auto">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Filter Berdasarkan</label>
                        <select name="filter_type" class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="monthly" {{ request('filter_type', 'monthly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="weekly" {{ request('filter_type') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="daily" {{ request('filter_type') == 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="all" {{ request('filter_type') == 'all' ? 'selected' : '' }}>Keseluruhan (Dari Awal)</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-auto" id="dateFilterContainer">
                        <label class="block text-xs font-bold text-slate-500 mb-1">Pilih Waktu</label>
                        <input 
                            type="{{ request('filter_type', 'monthly') == 'monthly' ? 'month' : (request('filter_type') == 'weekly' ? 'week' : 'date') }}" 
                            name="tanggal" 
                            id="tanggalFilter"
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

            <script>
                document.querySelector('select[name="filter_type"]').addEventListener('change', function() {
                    const dateInput = document.getElementById('tanggalFilter');
                    const container = document.getElementById('dateFilterContainer');
                    if (this.value === 'all') {
                        container.style.display = 'none';
                        dateInput.value = '';
                    } else {
                        container.style.display = 'block';
                        if (this.value === 'monthly') {
                            dateInput.type = 'month';
                        } else if (this.value === 'weekly') {
                            dateInput.type = 'week';
                        } else {
                            dateInput.type = 'date';
                        }
                        dateInput.value = ''; // clear on change
                    }
                });
                
                // Initialize display logic on load
                if(document.querySelector('select[name="filter_type"]').value === 'all') {
                    document.getElementById('dateFilterContainer').style.display = 'none';
                }
            </script>

            <div class="p-0 sm:p-0 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Karyawan</th>
                                <th class="px-6 py-4 text-center">Hadir</th>
                                <th class="px-6 py-4 text-center">Telat</th>
                                <th class="px-6 py-4 text-center">Izin</th>
                                <th class="px-6 py-4 text-center">Sakit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($summary as $index => $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full font-bold bg-slate-100 text-slate-600">
                                                #{{ $index + 1 }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-800">{{ $item['user']->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full {{ $item['hadir'] > 0 ? 'bg-emerald-100 text-emerald-700 font-bold' : 'bg-slate-50 text-slate-400' }}">
                                            {{ $item['hadir'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full {{ $item['telat'] > 0 ? 'bg-orange-100 text-orange-700 font-bold' : 'bg-slate-50 text-slate-400' }}">
                                            {{ $item['telat'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full {{ $item['izin'] > 0 ? 'bg-blue-100 text-blue-700 font-bold' : 'bg-slate-50 text-slate-400' }}">
                                            {{ $item['izin'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full {{ $item['sakit'] > 0 ? 'bg-rose-100 text-rose-700 font-bold' : 'bg-slate-50 text-slate-400' }}">
                                            {{ $item['sakit'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        Belum ada data absensi untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
@endsection
