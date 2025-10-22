@extends('layouts.app')

@section('title', 'Dashboard')
@section('page', 'QR-Code Absensi')
@section('card', 'Show QR')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>@yield('title')</h1>
            @include('partials.breadcrumb')
        </div>
        <div class="section-body">
            <h2 class="section-title">@yield('page')</h2>

            <div class="row">
                <div class="col-12 col-md-12 col-lg-12">
                    <div class="card card-primary">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="section-title mt-0 mb-0">@yield('card')</h4>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-primary d-flex align-items-center">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                        </div>
                        <div class="card-body">
                            
                            <!-- Data Absen-->
                            <div class="form-row">
                                <div class="form-group col-md-6 mt-0">
                                    <div class="w-100 w-lg-50">
                                        <table class="table table-border table-hover">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Absen</strong></td>
                                                    <td class="px-0"> : </td>
                                                    <td>{{ $qr->present == 'in_present' ? 'Masuk' : 'Keluar' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Shift</strong></td>
                                                    <td class="px-0"> : </td>
                                                    <td>{{ $qr->shift->shift_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jam Kerja</strong></td>
                                                    <td class="px-0"> : </td>
                                                    <td>{{ $qr->shift->in_time }} &mdash; {{ $qr->shift->out_time }}</td>
                                                <tr>
                                                    <td width="150"><strong>Tanggal</strong></td>
                                                    <td class="px-0"> : </td>
                                                    <td>{{ \Carbon\Carbon::parse($qr->date)->format('d M Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Aktif QR</strong></td>
                                                    <td class="px-0"> : </td>
                                                    <td>{{ \Carbon\Carbon::parse($qr->start_time)->format('H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Expired QR</strong></td>
                                                    <td class="px-0"> : </td>
                                                    <td>{{ \Carbon\Carbon::parse($qr->end_time)->format('H:i') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>ID QR</strong></td>
                                                    <td class="px-0"> : </td>
                                                    <td>{{ $qr->code_qr }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Status</strong></td>
                                                    <td class="px-0"> : </td>
                                                    <td>                                                       @if ($qr->status == 'active')
                                                            <span class="badge badge-success">Aktif</span>
                                                        @else
                                                            <span class="badge badge-danger">Expired</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- QR-Code Absen-->
                                <div class="form-group col-md-6 mt-0">
                                    <div class="d-flex justify-content-center align-items-center w-100 w-lg-50 mt-4 mt-lg-0">
                                        <div class="text-center p-4 rounded shadow-sm bg-light" 
                                            style="max-width: 280px; width: 100%; border: 1px solid #e0e0e0;">
                                            <h6 class="font-weight-bold mb-1">
                                                Absen {{ $qr->present == 'in_present' ? 'Masuk' : 'Keluar' }} - Shift {{ ucfirst($qr->shift->shift_name) }}
                                            </h6>
                                            <p class="mb-2 font-weight-bold small">{{ \Carbon\Carbon::parse($qr->date)->format('d M Y') }}</p>
                                            <div class="d-flex justify-content-center mb-2">
                                                {!! $showQR !!}
                                            </div>
                                            <p class="mt-2 text-muted small">{{ $qr->code_qr }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection