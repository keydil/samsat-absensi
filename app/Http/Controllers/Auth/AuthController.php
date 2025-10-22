<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'text' => 'required',
            'password' => 'required'
        ],[
            'text.required' => 'Username atau kode nomor karyawan harus diisi.',
            'password.required' => 'Password harus diisi.'
        ]);

        $datalogin = $request->only('text', 'password');

        // Coba login dengan username
        if (Auth::attempt(['username' => $datalogin['text'], 'password' => $datalogin['password']])) {
            return $this->redirectUser();
        }

        // Coba login dengan code_name
        if (Auth::attempt(['code_name' => $datalogin['text'], 'password' => $datalogin['password']])) {
            return $this->redirectUser();
        }

        return redirect()->back()->withErrors([
            'loginError' => 'Username, Kode User, atau Password yang dimasukkan tidak sesuai'
        ]);
    }
    

    protected function redirectUser()
    {
        $dataUser = Auth::user();

        if (Auth::user()->role == 'Admin') {
            return redirect('dashboard/admin')->with('info', 'Selamat datang ' . $dataUser->name);
        } elseif (Auth::user()->role == 'Karyawan') {
            return redirect('dashboard/karyawan')->with('info', 'Selamat datang ' . $dataUser->name);
        } 
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
