@extends('layouts.app')

@section('content')
    <div class="space-y-8">

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">QR Code Absensi</h1>
            <p class="text-sm text-slate-500">Buat QR Code baru untuk absensi pegawai sesuai shift kerja.</p>
        </div>

        @if (session('message'))
            <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
                <span class="font-medium">Berhasil!</span> {{ session('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Buat QR Code Baru</h3>
            </div>

            <form action="{{ route('admin.generate-qr.store') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tipe Absen</label>
                        <select name="present_type"
                            class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                            required>
                            <option value="" disabled selected>--- Pilih Tipe ---</option>
                            <option value="in_present">Absen Masuk</option>
                            <option value="out_present">Absen Pulang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tanggal Absen</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}"
                            class="w-full rounded-lg border-slate-200 px-3 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Mulai Scan (Jam)</label>
                        <input type="time" name="start_time"
                            class="w-full rounded-lg border-slate-200 px-3 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Selesai Scan (Jam)</label>
                        <input type="time" name="end_time"
                            class="w-full rounded-lg border-slate-200 px-3 py-2.5 text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>

                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition-all hover:bg-blue-700 hover:-translate-y-0.5">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Generate QR Code
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-white px-6 py-4">
                <h3 class="font-bold text-slate-800">Daftar QR Code Aktif</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500">
                        <tr>
                            <th class="px-6 py-4">#</th>
                            <th class="px-6 py-4">Tipe Absen</th>
                            <th class="px-6 py-4">Shift</th>
                            <th class="px-6 py-4">Jam Aktif</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($activeQr as $qr)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    @if ($qr->present == 'in_present')
                                        <span
                                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">Masuk</span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20">Pulang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    {{ $qr->shift->shift_name ?? 'Shift Harian' }}
                                </td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    {{ \Carbon\Carbon::parse($qr->start_time)->format('H:i') }} s/d
                                    {{ \Carbon\Carbon::parse($qr->end_time)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4">{{ $qr->date }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($qr->status == 'active')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                            Expired
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.generate-qr.show', $qr->code_qr) }}"
                                        class="inline-flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors shadow-sm">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat QR
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-10 w-10 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v1m6 11h2m-6 0h-2v4h-4v-4H8m13-4V7a1 1 0 00-1-1H4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <p>Belum ada QR Code aktif. Silahkan buat baru di atas.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
