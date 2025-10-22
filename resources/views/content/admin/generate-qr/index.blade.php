@extends('layouts.app')

@section('title', 'Dashboard')
@section('page', 'QR-Code Absensi')
@section('card', 'Generate QR')

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
                        <div class="card-header">
                            <h4 class="section-title mt-0 mb-0">@yield('card')</h4>
                        </div>
                        <div class="card-body">

                            <!-- Form Generate QR -->
                            <form action="{{ route('admin.generate-qr.store') }}" method="POST">
                                @csrf
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Pilih Tipe Absen</label>
                                        <select name="present_type" class="form-control">
                                            <option value="">--- Pilih Tipe Absen ---</option>
                                            <option value="in_present">Absen Masuk</option>
                                            <option value="out_present">Absen Keluar</option>
                                        </select>
                                        @error('present_type') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Shift Kerja</label>
                                        <select name="shift_id" class="form-control">
                                            <option value="">--- Pilih Shift Kerja ---</option>
                                            @foreach($shifts as $shift)
                                                <option value="{{ $shift->id }}">
                                                    Shift {{ $shift->shift_name }} 
                                                    ({{ \Carbon\Carbon::parse($shift->in_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->out_time)->format('H:i') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('shift_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Tanggal Absen</label>
                                        <input type="date" name="date" class="form-control" placeholder="Silahkan isi keterangan waktu">
                                        @error('date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Mulai Aktif</label> 
                                        <input type="time" name="start_time" class="form-control" placeholder="Silahkan isi keterangan waktu">
                                        @error('start_time') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Berakhir Aktif</label>
                                        <input type="time" name="end_time" class="form-control" placeholder="Silahkan isi keterangan waktu">
                                        @error('end_time') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Tampilkan QR-Code Absensi</label>
                                        <button type="submit" class="btn btn-lg btn-primary btn-block">
                                            <i class="fas fa-qrcode"></i> Generate QR-Code
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if (session('message'))
                                <div class="alert alert-success">{{ session('message') }}</div>
                            @endif

                            @if (session('qr_code_value'))
                                <div class="d-flex justify-content-center mb-4 mt-4">
                                    <div class="text-center p-4 rounded" style="background: #f8f9fa; box-shadow: 0 2px 6px rgba(0,0,0,0.1); width: fit-content;">
                                        {{-- Judul di atas QR --}}
                                        <h6 class="font-weight-bold mb-3">
                                            Absen 
                                            {{ session('qr_present_type') === 'in_present' ? 'Masuk' : 'Keluar' }} 
                                            Shift {{ ucfirst(session('qr_shift_name')) }}
                                        </h6>
                                        <p class="font-weight-bold mb-3">
                                            {{ session('date') }}
                                        </p>
                                        {{-- QR Code --}}
                                        <div class="d-flex justify-content-center mb-2">
                                            {!! QrCode::size(200)->generate(session('qr_code_value')) !!}
                                        </div>
                                        {{-- UUID di bawah QR --}}
                                        <span class="text-muted small mb-0">{{ session('qr_code_value') }}</span>
                                    </div>
                                </div>
                            @endif

                            <hr class="my-4">

                            <!-- Tabel QR -->
                            <h4 class="section-title mt-0 mb-0">Daftar QR-Code Absensi</h4>
                            <div class="table-responsive mt-3">
                                <table class="table table-borderless table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Absen</th>
                                            <th>Shift</th>
                                            <th>Jam Kerja</th>
                                            <th>Tanggal</th>
                                            <th>Aktif QR</th>
                                            <th>Expired QR</th>
                                            <th>Status</th>
                                            <th>Lihat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($activeQr as $qr)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $qr->present == 'in_present' ? 'Masuk' : 'Keluar' }}</td>
                                                <td>{{ $qr->shift->shift_name ?? '-' }}</td>
                                                <td>{{ $qr->shift->in_time }} &mdash; {{ $qr->shift->out_time }}</td>
                                                <td>{{ \Carbon\Carbon::parse($qr->date)->format('d-m-Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($qr->start_time)->format('H:i') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($qr->end_time)->format('H:i') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $qr->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($qr->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button onclick="window.location.href='{{ route('admin.generate-qr.show', $qr->code_qr) }}'" class="btn btn-sm btn-info">
                                                        <span class="fas fa-eye"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">Tidak ada QR aktif saat ini</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')

@endpush
