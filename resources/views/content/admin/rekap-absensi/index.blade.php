@extends('layouts.app')

@section('content')
    <div class="space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Rekap Absensi</h1>
                <p class="text-sm text-slate-500">Pantau kehadiran pegawai secara realtime.</p>
            </div>

            <div class="flex items-center gap-3">
                <form action="{{ route('admin.rekap-absensi.clear-old') }}" method="POST" id="form-clear-old">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmClearOld()" class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-rose-700 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Bersihkan Data Lama
                    </button>
                </form>

                <a href="{{ route('admin.rekap-absensi.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Excel
                </a>
            </div>
        </div>

        <div x-data="{ activeTab: 'semua' }" class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            
            {{-- Tabs Navigation --}}
            <div class="border-b border-slate-200 bg-slate-50 flex overflow-x-auto">
                <button @click="activeTab = 'semua'" 
                    :class="activeTab === 'semua' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold bg-white' : 'text-slate-500 font-medium hover:bg-slate-100 hover:text-slate-700'"
                    class="px-6 py-4 text-sm transition-colors whitespace-nowrap focus:outline-none">
                    Semua Data Absensi
                </button>
                <button @click="activeTab = 'pending'" 
                    :class="activeTab === 'pending' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold bg-white' : 'text-slate-500 font-medium hover:bg-slate-100 hover:text-slate-700'"
                    class="px-6 py-4 text-sm transition-colors whitespace-nowrap focus:outline-none flex items-center gap-2">
                    Menunggu Persetujuan
                    @if(count($absensi_pending) > 0)
                        <span class="inline-flex items-center justify-center h-5 w-5 rounded-full bg-rose-500 text-[10px] font-bold text-white">{{ count($absensi_pending) }}</span>
                    @endif
                </button>
            </div>

            {{-- TAB 1: Semua Data --}}
            <div x-show="activeTab === 'semua'">
                <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                    <form action="{{ route('admin.rekap-absensi') }}" method="GET" class="flex flex-col sm:flex-row flex-wrap gap-4 items-end">
                        <div class="w-full sm:w-auto flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-500 mb-1">Tipe Filter Waktu</label>
                            <select name="filter_type" class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="daily" {{ request('filter_type', 'daily') == 'daily' ? 'selected' : '' }}>Harian</option>
                                <option value="weekly" {{ request('filter_type') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                <option value="monthly" {{ request('filter_type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            </select>
                        </div>

                        <div class="w-full sm:w-auto flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-500 mb-1">Pilih Waktu</label>
                            <input type="{{ request('filter_type') == 'monthly' ? 'month' : (request('filter_type') == 'weekly' ? 'week' : 'date') }}" 
                                name="tanggal"
                                id="tanggalFilter"
                                class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500"
                                value="{{ request('tanggal') ?? date('Y-m-d') }}">
                        </div>

                        <div class="w-full sm:w-auto flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold text-slate-500 mb-1">Karyawan</label>
                            <select name="user_id" class="w-full rounded-lg border-slate-200 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Semua Karyawan --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="submit"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition-colors w-full sm:w-auto flex-1">
                                Terapkan Filter
                            </button>

                            @if (request('tanggal') || request('user_id') || request('filter_type'))
                                <a href="{{ route('admin.rekap-absensi') }}"
                                    class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 transition-colors text-center w-full sm:w-auto flex-1">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <script>
                    document.querySelector('select[name="filter_type"]').addEventListener('change', function() {
                        const dateInput = document.getElementById('tanggalFilter');
                        if (this.value === 'monthly') {
                            dateInput.type = 'month';
                        } else if (this.value === 'weekly') {
                            dateInput.type = 'week';
                        } else {
                            dateInput.type = 'date';
                        }
                        dateInput.value = ''; // clear on change to avoid format clash
                    });
                </script>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">Pegawai</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Jam Masuk</th>
                            <th class="px-6 py-4">Jam Pulang</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
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

                                @if(in_array($item->status, ['Izin', 'Sakit']))
                                    <td colspan="3" class="p-4">
                                        @if($item->status === 'Izin')
                                            <div class="relative flex items-center justify-between rounded-xl border border-blue-100 bg-blue-50/50 p-3 transition-colors hover:bg-blue-50">
                                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-400 rounded-l-xl"></div>
                                                <div class="flex items-center gap-4 pl-3">
                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-100 text-blue-600">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                                    </div>
                                        @else
                                            <div class="relative flex items-center justify-between rounded-xl border border-rose-100 bg-rose-50/50 p-3 transition-colors hover:bg-rose-50">
                                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-rose-400 rounded-l-xl"></div>
                                                <div class="flex items-center gap-4 pl-3">
                                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-100 text-rose-600">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                                    </div>
                                        @endif
                                                    <div>
                                                        <div class="flex items-center gap-2.5">
                                                            <span class="font-bold {{ $item->status === 'Izin' ? 'text-blue-700' : 'text-rose-700' }}">{{ $item->status }}</span>
                                                            
                                                            @if($item->approval_status == 'pending')
                                                                <span class="inline-flex items-center gap-1.5 rounded bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-amber-700">
                                                                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span></span>
                                                                    Menunggu
                                                                </span>
                                                            @elseif($item->approval_status == 'rejected')
                                                                <span class="inline-flex items-center gap-1.5 rounded bg-rose-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-rose-700">
                                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                                    Ditolak
                                                                </span>
                                                            @elseif($item->approval_status == 'approved')
                                                                <span class="inline-flex items-center gap-1.5 rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-emerald-700">
                                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                                    Disetujui
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <p class="mt-1 text-xs italic text-slate-500 line-clamp-1 max-w-[300px]">
                                                            "{{ $item->status_desc ?? 'Tanpa keterangan' }}"
                                                        </p>
                                                    </div>
                                                </div>

                                                @if($item->bukti_surat)
                                                    <div class="pr-2">
                                                        <a href="{{ $item->bukti_surat }}" target="_blank" class="group flex items-center gap-2 rounded-lg bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 transition-all hover:bg-slate-50 hover:ring-slate-300">
                                                            <svg class="h-4 w-4 text-slate-400 transition-colors group-hover:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                                            Cek Dokumen
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                    </td>
                                @else
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
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Hadir</span>
                                            @elseif($item->status === 'Telat')
                                                <span class="inline-flex items-center rounded-full bg-orange-50 px-2.5 py-1 text-xs font-medium text-orange-700 ring-1 ring-inset ring-orange-600/20">Telat</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700 ring-1 ring-inset ring-slate-600/20">{{ $item->status }}</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('admin.rekap-absensi.destroy', ['user_id' => $item->user_id, 'date' => $item->date]) }}" method="POST" class="form-delete" data-name="{{ $item->user->name ?? 'User' }}" data-date="{{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors" title="Hapus Data">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
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
            </div> {{-- End TAB 1 --}}

            {{-- TAB 2: Menunggu Persetujuan --}}
            <div x-show="activeTab === 'pending'" x-cloak>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-amber-50 text-xs uppercase font-bold text-amber-700 border-b border-amber-100">
                            <tr>
                                <th class="px-6 py-4">Pegawai</th>
                                <th class="px-6 py-4">Tanggal Pengajuan</th>
                                <th class="px-6 py-4">Status & Keterangan</th>
                                <th class="px-6 py-4 text-center">Bukti Surat</th>
                                <th class="px-6 py-4 text-center">Aksi (Persetujuan)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($absensi_pending as $pending)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                {{ substr($pending->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $pending->user->name ?? 'User Terhapus' }}</p>
                                                <p class="text-xs text-slate-400">{{ $pending->user->email ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-700">
                                        {{ \Carbon\Carbon::parse($pending->created_at)->translatedFormat('d F Y, H:i') }}
                                        <p class="text-[10px] text-slate-400 uppercase mt-1">Untuk Absen: {{ \Carbon\Carbon::parse($pending->date)->translatedFormat('d F Y') }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($pending->status === 'Izin')
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-blue-700 mb-1">Izin</span>
                                        @elseif($pending->status === 'Sakit')
                                            <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-rose-700 mb-1">Sakit</span>
                                        @endif
                                        <p class="text-xs text-slate-600 mt-1 italic">"{{ $pending->status_desc ?? 'Tanpa keterangan' }}"</p>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($pending->bukti_surat)
                                            <button type="button" onclick="openBuktiModal('{{ $pending->bukti_surat }}', '{{ $pending->id }}', '{{ addslashes($pending->user->name ?? 'User Terhapus') }}', '{{ $pending->status }}')" class="inline-flex items-center gap-1 rounded bg-white px-3 py-1.5 text-xs font-semibold text-blue-600 shadow-sm ring-1 ring-inset ring-blue-300 hover:bg-blue-50 transition">
                                                🖼️ Cek Surat
                                            </button>
                                        @else
                                            <span class="text-xs text-slate-400">Tidak ada file</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Form Approve --}}
                                            <form id="form-approve-{{ $pending->id }}" action="{{ route('admin.rekap-absensi.approve', $pending->id) }}" method="POST">
                                                @csrf
                                                <button type="button" onclick="confirmApprove('{{ $pending->id }}')" class="flex items-center justify-center h-8 w-8 rounded bg-emerald-100 text-emerald-600 hover:bg-emerald-200 transition" title="Setujui">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                </button>
                                            </form>

                                            {{-- Form Reject --}}
                                            <form id="form-reject-{{ $pending->id }}" action="{{ route('admin.rekap-absensi.reject', $pending->id) }}" method="POST">
                                                @csrf
                                                <button type="button" onclick="confirmReject('{{ $pending->id }}')" class="flex items-center justify-center h-8 w-8 rounded bg-rose-100 text-rose-600 hover:bg-rose-200 transition" title="Tolak">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="font-medium">Tidak ada pengajuan yang perlu persetujuan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div> {{-- End TAB 2 --}}

        </div>
    </div>

    {{-- MODAL BUKTI SURAT --}}
    <div id="buktiModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" onclick="closeBuktiModal()"></div>
        
        <!-- Modal Panel -->
        <div class="relative w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <span class="text-2xl" id="modalIcon">📄</span>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800" id="modalTitle">Bukti Surat</h3>
                        <p class="text-xs text-slate-500" id="modalSubtitle">Pegawai: -</p>
                    </div>
                </div>
                <button onclick="closeBuktiModal()" class="rounded-lg p-2 text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Body (Image) -->
            <div class="overflow-y-auto p-4 flex-1 flex flex-col items-center justify-center bg-slate-100/50 min-h-[300px] w-full relative">
                <!-- Loading State -->
                <div id="modalLoading" class="hidden flex-col items-center justify-center absolute inset-0 bg-slate-100/80 z-10">
                    <svg class="animate-spin h-8 w-8 text-blue-600 mb-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span class="text-sm text-slate-500 font-medium">Memuat Dokumen...</span>
                </div>
                
                <img id="modalImage" src="" alt="Bukti Surat" class="max-w-full max-h-[60vh] object-contain rounded-lg shadow-sm border border-slate-200 hidden">
                <iframe id="modalPdf" src="" class="hidden w-full h-[60vh] rounded-lg shadow-sm border border-slate-200 bg-white"></iframe>
            </div>

            <!-- Footer (Action Buttons) -->
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-end gap-3 shrink-0">
                <a href="#" id="modalBtnNewTab" target="_blank" class="rounded-lg px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors mr-auto">
                    Buka di Tab Baru ↗
                </a>
                <button type="button" onclick="closeBuktiModal()" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 transition-colors">
                    Tutup
                </button>
                <button type="button" id="modalBtnReject" class="rounded-lg px-4 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    Tolak
                </button>
                <button type="button" id="modalBtnApprove" class="rounded-lg px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Setujui
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Modal Bukti Logic
        function openBuktiModal(imgUrl, absensiId, userName, statusType) {
            const img = document.getElementById('modalImage');
            const pdf = document.getElementById('modalPdf');
            const loading = document.getElementById('modalLoading');
            
            // Reset state
            img.classList.add('hidden');
            pdf.classList.add('hidden');
            loading.classList.remove('hidden');
            loading.classList.add('flex');
            img.src = '';
            pdf.src = '';

            document.getElementById('modalBtnNewTab').href = imgUrl;
            document.getElementById('modalTitle').textContent = `Bukti Surat ${statusType}`;
            document.getElementById('modalSubtitle').textContent = `Pegawai: ${userName}`;
            document.getElementById('modalIcon').textContent = statusType === 'Sakit' ? '🏥' : '📝';
            
            // Re-bind actions
            document.getElementById('modalBtnApprove').onclick = () => { closeBuktiModal(); confirmApprove(absensiId); };
            document.getElementById('modalBtnReject').onclick = () => { closeBuktiModal(); confirmReject(absensiId); };
            
            const modal = document.getElementById('buktiModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';

            // Smart Document Loader
            if (imgUrl.toLowerCase().includes('.pdf')) {
                // Render PDF pake Google Docs Viewer (Support Mobile)
                pdf.src = `https://docs.google.com/gview?url=${encodeURIComponent(imgUrl)}&embedded=true`;
                pdf.onload = () => { 
                    loading.classList.add('hidden'); loading.classList.remove('flex'); 
                    pdf.classList.remove('hidden'); 
                };
            } else {
                // Coba render sebagai gambar
                img.src = imgUrl;
                img.onload = () => { 
                    loading.classList.add('hidden'); loading.classList.remove('flex'); 
                    img.classList.remove('hidden'); 
                };
                img.onerror = () => {
                    // Kalo gambar gagal diload (mungkin PDF tanpa ekstensi), fallback ke PDF Viewer
                    pdf.src = `https://docs.google.com/gview?url=${encodeURIComponent(imgUrl)}&embedded=true`;
                    pdf.onload = () => { 
                        loading.classList.add('hidden'); loading.classList.remove('flex'); 
                        pdf.classList.remove('hidden'); 
                    };
                };
            }
        }

        function closeBuktiModal() {
            const modal = document.getElementById('buktiModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
            document.getElementById('modalImage').src = '';
        }
        // Notifikasi Sukses dari Controller
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session("success") }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Konfirmasi Hapus Satuan
        document.querySelectorAll('.form-delete').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const name = this.getAttribute('data-name');
                const date = this.getAttribute('data-date');
                Swal.fire({
                    title: 'Hapus Absensi?',
                    html: `Anda yakin ingin menghapus absensi <b>${name}</b> pada tanggal <b>${date}</b>?<br><br>Seluruh data Masuk & Pulang di hari tersebut akan dihapus permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Konfirmasi Bersihkan Data Lama
        function confirmClearOld() {
            Swal.fire({
                title: 'Bersihkan Data Lama?',
                html: `Aksi ini akan menghapus <b>semua riwayat absensi</b> yang usianya lebih dari 30 hari secara permanen.<br><br>Sangat disarankan untuk melakukan <b>Export Excel</b> terlebih dahulu sebagai *backup*.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Bersihkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-clear-old').submit();
                }
            });
        }

        function confirmApprove(id) {
            Swal.fire({
                title: 'Setujui Pengajuan?',
                text: "Apakah Anda yakin ingin menyetujui pengajuan Izin/Sakit ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-approve-' + id).submit();
                }
            });
        }

        function confirmReject(id) {
            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: "Apakah Anda yakin ingin MENOLAK pengajuan Izin/Sakit ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-reject-' + id).submit();
                }
            });
        }
    </script>
@endpush