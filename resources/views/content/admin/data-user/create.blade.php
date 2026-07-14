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
                    <div class="relative">
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg @error('name') border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50 pr-10 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror transition-all" placeholder="Contoh: Asep Saepuloh" required>
                        @error('name')
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                        </div>
                        @enderror
                    </div>
                    @error('name')
                        <p class="text-xs font-medium text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username / NIP</label>
                    <div class="relative">
                        <input type="text" name="username" value="{{ old('username') }}" class="w-full rounded-lg @error('username') border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50 pr-10 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror transition-all" placeholder="19900101..." required>
                        @error('username')
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                        </div>
                        @enderror
                    </div>
                    @error('username')
                        <p class="text-xs font-medium text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode User (Unik)</label>
                    <div class="relative">
                        <input type="text" name="code_name" value="{{ old('code_name') }}" class="w-full rounded-lg @error('code_name') border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50 pr-10 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror transition-all" placeholder="Contoh: USR005" required>
                        @error('code_name')
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                        </div>
                        @enderror
                    </div>
                    @error('code_name')
                        <p class="text-xs font-medium text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jabatan / Role</label>
                    <select name="role" class="w-full rounded-lg @error('role') border-rose-300 text-rose-900 focus:border-rose-500 focus:ring-rose-500 bg-rose-50 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror transition-all cursor-pointer">
                        <option value="Karyawan" {{ old('role') == 'Karyawan' ? 'selected' : '' }}>Pegawai Biasa</option>
                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="Kepala" {{ old('role') == 'Kepala' ? 'selected' : '' }}>Kepala Kantor</option>
                    </select>
                    @error('role')
                        <p class="text-xs font-medium text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nomor WhatsApp</label>
                    <div class="relative">
                        <input type="number" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg @error('phone') border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50 pr-10 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror transition-all" placeholder="628123456789" required>
                        @error('phone')
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                        </div>
                        @enderror
                    </div>
                    @error('phone')
                        <p class="text-xs font-medium text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Alamat Email</label>
                    <div class="relative">
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg @error('email') border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50 pr-10 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 @enderror transition-all" placeholder="email@contoh.com" required>
                        @error('email')
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                        </div>
                        @enderror
                    </div>
                    @error('email')
                        <p class="text-xs font-medium text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <input x-bind:type="show ? 'text' : 'password'" name="password" class="w-full rounded-lg @error('password') border-rose-300 text-rose-900 placeholder-rose-300 focus:border-rose-500 focus:ring-rose-500 bg-rose-50 pr-10 @else border-slate-200 focus:border-blue-500 focus:ring-blue-500 pr-10 @enderror transition-all" placeholder="••••••••" required>
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs font-medium text-rose-500 mt-1.5">{{ $message }}</p>
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