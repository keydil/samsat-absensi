@extends('layouts.app')

@section('content')
    <div class="space-y-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Detail QR Code</h1>
                <p class="text-sm text-slate-500">Informasi detail mengenai QR Code absensi yang dipilih.</p>
            </div>
            <a href="{{ route('admin.generate-qr') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-white border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
            <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
                <h3 class="font-semibold text-slate-800">Data Absensi QR</h3>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-2 space-y-6">
                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200">
                                <tbody class="divide-y divide-slate-200 bg-white">

                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-500 w-1/3">
                                            Jenis Absen</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-900 font-bold">
                                            @if ($qr->present == 'in_present')
                                                <span
                                                    class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10">Masuk</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-medium bg-orange-50 text-orange-700 ring-1 ring-inset ring-orange-600/20">Pulang</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-500">Shift
                                            Kerja</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-900 font-semibold">
                                            {{ $qr->shift->shift_name ?? 'Shift Harian' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-500">Jam
                                            Operasional Shift</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                                            @if ($qr->shift)
                                                {{ \Carbon\Carbon::parse($qr->shift->in_time)->format('H:i') }}
                                                <span class="text-slate-400 mx-1">&mdash;</span>
                                                {{ \Carbon\Carbon::parse($qr->shift->out_time)->format('H:i') }} WIB
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-500">Tanggal
                                            Absen</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                                            {{ \Carbon\Carbon::parse($qr->date)->translatedFormat('l, d F Y') }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-500">Mulai
                                            Scan (Aktif)</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-emerald-600 font-bold">
                                            {{ \Carbon\Carbon::parse($qr->start_time)->format('H:i') }} WIB
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-500">Selesai
                                            Scan (Expired)</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-red-600 font-bold">
                                            {{ \Carbon\Carbon::parse($qr->end_time)->format('H:i') }} WIB
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-slate-500">Status QR
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            @if ($qr->status == 'active')
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                    <span
                                                        class="h-1.5 w-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                                    Aktif
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">
                                                    Expired
                                                </span>
                                            @endif
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div
                            class="flex flex-col items-center justify-center rounded-xl bg-slate-50 p-6 border border-slate-200 border-dashed h-full">

                            <div class="text-center mb-4">
                                <h4 class="text-lg font-bold text-slate-800">Scan Disini</h4>
                                <p class="text-xs text-slate-500">Arahkan kamera HP ke QR Code ini</p>
                            </div>

                            <div class="bg-white p-4 rounded-lg shadow-sm border border-slate-100 mb-4">
                                {!! $showQR !!}
                            </div>

                            <div class="text-center w-full">
                                <p class="text-[10px] font-mono text-slate-400 uppercase tracking-wider mb-1">QR ID</p>
                                <div
                                    class="bg-slate-200/50 rounded px-2 py-1 text-xs font-mono text-slate-600 break-all select-all cursor-text">
                                    {{ $qr->code_qr }}
                                </div>
                            </div>

                            <button onclick="window.print()"
                                class="mt-6 flex w-full items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800 transition-colors">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                Print QR Code
                            </button>

                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
