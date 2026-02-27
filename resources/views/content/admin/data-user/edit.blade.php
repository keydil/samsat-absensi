@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Pegawai</h1>
        <p class="text-sm text-slate-500">Perbarui data pegawai di bawah ini.</p>
    </div>

    @if ($errors->any())
        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-slate-100 bg-slate-50/50 px-6 py-4">
            <h3 class="font-semibold text-slate-800">Form Edit Pegawai</h3>
        </div>
        <form action="{{ route('admin.dataUser.update', $user) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                        class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Pegawai</label>
                    <input type="text" name="code_name" value="{{ old('code_name', $user->code_name) }}"
                        class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">No. HP (WhatsApp)</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                    <select name="role" class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700" required>
                        <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="Karyawan" {{ $user->role == 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">
                        Password Baru <span class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span>
                    </label>
                    <input type="password" name="password"
                        class="w-full rounded-lg border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-700"
                        placeholder="Minimal 6 karakter">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.dataUser') }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-lg border border-slate-200 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center px-5 py-2.5 rounded-lg bg-blue-600 text-sm font-bold text-white shadow-md shadow-blue-500/20 hover:bg-blue-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection