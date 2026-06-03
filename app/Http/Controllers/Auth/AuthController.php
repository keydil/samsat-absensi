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
        $user = \App\Models\User::where('username', $datalogin['text'])
                    ->orWhere('code_name', $datalogin['text'])
                    ->first();

        if (!$user) {
            return redirect()->back()->withErrors([
                'loginError' => 'Username atau Kode User tidak terdaftar.'
            ])->withInput($request->except('password'));
        }

        if (!\Illuminate\Support\Facades\Hash::check($datalogin['password'], $user->password)) {
            return redirect()->back()->withErrors([
                'loginError' => 'Password yang Anda masukkan salah.'
            ])->withInput($request->except('password'));
        }

        Auth::login($user);
        return $this->redirectUser();

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
