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
        <form action="{{ route('admin.dataUser.store') }}" method="POST" class="p-6 space-y-6" x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
            @csrf

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('name') border-rose-500 @enderror" placeholder="Contoh: Asep Saepuloh" required>
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username / NIP</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('username') border-rose-500 @enderror" placeholder="19900101..." required>
                    @error('username')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode User (Unik)</label>
                    <input type="text" name="code_name" value="{{ old('code_name') }}" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('code_name') border-rose-500 @enderror" placeholder="Contoh: USR005" required>
                    @error('code_name')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jabatan / Role</label>
                    <select name="role" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('role') border-rose-500 @enderror">
                        <option value="Karyawan" {{ old('role') == 'Karyawan' ? 'selected' : '' }}>Pegawai Biasa</option>
                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="Kepala" {{ old('role') == 'Kepala' ? 'selected' : '' }}>Kepala Kantor</option>
                    </select>
                    @error('role')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('phone') border-rose-500 @enderror" placeholder="628123456789">
                    @error('phone')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('email') border-rose-500 @enderror" placeholder="email@contoh.com" required>
                    @error('email')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 @error('password') border-rose-500 @enderror" placeholder="••••••••" required>
                    @error('password')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.dataUser') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 rounded-lg">Batal</a>
                <button type="submit" x-bind:disabled="isSubmitting" class="px-6 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-70 disabled:cursor-not-allowed rounded-lg shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5">
                    <span x-show="!isSubmitting">Simpan Pegawai</span>
                    <span x-show="isSubmitting" class="flex items-center justify-center gap-2" x-cloak>
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>

        </form>
    </div>
</div>
@endsection