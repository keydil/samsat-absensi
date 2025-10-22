@extends('layouts.app')

@section('title', 'Dashboard')
@section('page', 'QR-Code Absensi')
@section('card', 'Scan QR')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
<style>
    #reader {
        width: 260px;
        height: 260px;
        margin: 0 auto;
        border-radius: 10px;
        overflow: hidden;
    }
    #absen-form {
        margin-top: 25px;
        display: none;
    }
</style>
@endpush

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

                        {{-- Kamera QR --}}
                        <div id="reader"></div>
                        <div class="mt-2 text-center">
                            <button id="start-scan" class="btn btn-primary">
                                <i class="fas fa-qrcode"></i> Mulai Scan QR-Code
                            </button>
                            <button id="stop-scan" class="btn btn-danger d-none">
                                <i class="fas fa-stop"></i> Stop Scan
                            </button>
                        </div>

                        {{-- Form Absensi --}}
                        <form id="absen-form" class="mt-4">
                            @csrf
                            <input type="hidden" name="code_qr" id="code_qr">
                            <input type="hidden" name="shift_id" id="shift_id">
                            <input type="hidden" name="date" id="date" value="{{ now()->format('Y-m-d') }}">
                            <input type="hidden" name="time" id="time" value="{{ now()->format('H:i:s') }}">

                            <div class="row justify-content-center">
                                <div class="col-md-6">
                                    <div class="form-group text-center">
                                        <label>Status Kehadiran</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="">--- Pilih Kehadiran ---</option>
                                            <option value="Hadir">Hadir</option>
                                            <option value="Izin">Izin</option>
                                            <option value="Sakit">Sakit</option>
                                        </select>
                                    </div>
                                    <div class="form-group" id="desc-group" style="display:none;">
                                        <label>Keterangan</label>
                                        <textarea name="status_desc" id="status_desc" class="form-control" placeholder="Isi jika izin atau sakit"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-block mt-3">
                                        <i class="fas fa-save"></i> Simpan Absensi
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr class="my-4">

                        {{-- Tabel absensi --}}
                        <h4 class="section-title mt-0 mb-0">Daftar Absensi</h4>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tanggal</th>
                                        <th>Shift</th>
                                        <th>Kehadiran</th>
                                        <th>Keterangan</th>
                                        <th>Jam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($absens as $absen)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ \Carbon\Carbon::parse($absen->date)->format('d M Y') }}</td>
                                            <td>{{ $absen->shift->shift_name }}</td>
                                            <td>
                                                <span class="badge badge-{{ $absen->status == 'Hadir' ? 'success' : ($absen->status == 'Izin' ? 'warning' : 'danger') }}">
                                                    {{ $absen->status }}
                                                </span>
                                            </td>
                                            <td>{{ $absen->status_desc ?? '-' }}</td>
                                            <td>{{ $absen->time }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center text-muted">Belum ada absensi</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    let html5QrCode;
    const startBtn = document.getElementById("start-scan");
    const stopBtn = document.getElementById("stop-scan");
    const formAbsen = document.getElementById("absen-form");
    const codeInput = document.getElementById("code_qr");

    // === START SCAN ===
    startBtn.addEventListener("click", async () => {
        try {
            html5QrCode = new Html5Qrcode("reader");
            startBtn.classList.add("d-none");
            stopBtn.classList.remove("d-none");

            await html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 200 },
                qrCodeMessage => {
                    html5QrCode.stop().then(() => {
                        iziToast.success({
                            title: 'QR Terdeteksi',
                            message: 'Kode: ' + qrCodeMessage,
                            position: 'topRight'
                        });

                        codeInput.value = qrCodeMessage;
                        formAbsen.style.display = "block";
                        stopBtn.classList.add("d-none");
                        startBtn.classList.remove("d-none");

                        // Optional: auto-fetch shift_id dari backend (jika QR berisi shift info)
                        fetch("{{ route('user.scanCheck') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ code_qr: qrCodeMessage })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success && data.data.shift_id) {
                                document.getElementById("shift_id").value = data.data.shift_id;
                            }
                        });
                    });
                },
                err => {}
            );
        } catch (err) {
            console.error("Error kamera:", err);
            alert("Tidak bisa mengakses kamera: " + (err.message || err));
            startBtn.classList.remove("d-none");
            stopBtn.classList.add("d-none");
        }
    });

    // === STOP SCAN ===
    stopBtn.addEventListener("click", async () => {
        if (html5QrCode) {
            try {
                await html5QrCode.stop();
                html5QrCode.clear();
                iziToast.info({ title: 'Scan Dihentikan', message: 'Kamera dimatikan', position: 'topRight' });
            } catch (err) {
                console.warn("Tidak bisa menghentikan kamera:", err);
            }
        }
        startBtn.classList.remove("d-none");
        stopBtn.classList.add("d-none");
    });

    // === TAMPILKAN KETERANGAN ===
    document.getElementById("status").addEventListener("change", (e) => {
        if (["Izin", "Sakit"].includes(e.target.value)) {
            document.getElementById("desc-group").style.display = "block";
        } else {
            document.getElementById("desc-group").style.display = "none";
        }
    });

    // === SUBMIT ABSENSI ===
    document.getElementById("form-absen").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch("{{ route('user.scanStore') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                iziToast.success({ title: 'Berhasil', message: data.message, position: 'topRight' });
                formAbsen.reset();
                formAbsen.style.display = "none";
                setTimeout(() => location.reload(), 1000);
            } else {
                iziToast.error({ title: 'Gagal', message: data.message, position: 'topRight' });
            }
        })
        .catch(() => {
            iziToast.error({ title: 'Error', message: 'Gagal menyimpan absensi', position: 'topRight' });
        });
    });
});
</script>
@endpush
