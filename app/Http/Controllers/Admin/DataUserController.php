<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\User;
use Illuminate\Http\Request;

class DataUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'karyawan')->get();
        return view('content.admin.data-user.index', (compact('users')));
    }
}
