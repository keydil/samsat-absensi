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
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'code_name' => 'required|string|unique:users,code_name',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string',
            'role' => 'required|in:Admin,Karyawan',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'code_name' => $request->code_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,

            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.dataUser')->with('success', 'Pegawai berhasil ditambahkan!');
    }
}
