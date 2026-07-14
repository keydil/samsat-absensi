<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DataUserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('content.admin.data-user.index', compact('users'));
    }

    public function create()
    {
        return view('content.admin.data-user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|unique:users,username',
            'code_name' => 'required|string|unique:users,code_name',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required|string|min:10|max:15',
            'role'      => 'required|in:Admin,Karyawan,Kepala',
            'password'  => 'required|string|min:6',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'NIP / Username wajib diisi.',
            'username.unique' => 'NIP / Username ini sudah terdaftar di sistem.',
            'code_name.required' => 'Kode User wajib diisi.',
            'code_name.unique' => 'Kode User ini sudah dipakai, gunakan kode lain.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone.required' => 'Nomor WA wajib diisi untuk keperluan komunikasi HRD.',
            'phone.min' => 'Nomor WA minimal 10 digit angka.',
            'role.required' => 'Jabatan wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'name'      => $request->name,
            'username'  => $request->username,
            'code_name' => $request->code_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'role'      => $request->role,
            'password'  => Hash::make($request->password),
        ]);

        return redirect()->route('admin.dataUser')->with('success', 'Pegawai berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        return view('content.admin.data-user.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|unique:users,username,' . $user->id,
            'code_name' => 'required|string|unique:users,code_name,' . $user->id,
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'phone'     => 'required|string|min:10|max:15',
            'role'      => 'required|in:Admin,Karyawan,Kepala',
            'password'  => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'NIP / Username wajib diisi.',
            'username.unique' => 'NIP / Username ini sudah terdaftar di sistem.',
            'code_name.required' => 'Kode User wajib diisi.',
            'code_name.unique' => 'Kode User ini sudah dipakai.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'phone.required' => 'Nomor WA wajib diisi untuk keperluan komunikasi HRD.',
            'phone.min' => 'Nomor WA minimal 10 digit angka.',
            'role.required' => 'Jabatan wajib dipilih.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $data = [
            'name'      => $request->name,
            'username'  => $request->username,
            'code_name' => $request->code_name,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'role'      => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.dataUser')->with('success', 'Data pegawai berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.dataUser')->with('success', 'Pegawai berhasil dihapus!');
    }
}