@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Pegawai Baru</h1>
            <p class="text-sm text-slate-500">Isi formulir berikut untuk mendaftarkan pegawai ke dalam sistem.</p>
        </div>
        <a href="{{ route('admin.dataUser') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">
            &larr; Kembali
        </a>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <form action="{{ route('admin.dataUser.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Asep Saepuloh" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username / NIP</label>
                    <input type="text" name="username" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="19900101..." required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode User (Unik)</label>
                    <input type="text" name="code_name" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: USR005" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jabatan / Role</label>
                    <select name="role" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                        <option value="Karyawan">Pegawai Biasa</option>
                        <option value="Admin">Administrator</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                    <input type="text" name="phone" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="628123456789">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="email@contoh.com" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500" placeholder="••••••••" required>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.dataUser') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Batal</a>
                <button type="submit" class="px-6 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5">
                    Simpan Pegawai
                </button>
            </div>

        </form>
    </div>
</div>
@endsection