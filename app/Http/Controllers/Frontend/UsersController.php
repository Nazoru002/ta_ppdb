<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;

class UsersController extends Controller
{
    public function index()
    {
        return "Ok !";
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        try {
            $login = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
            if ($login == true) {
                $user = User::where('email', '=', $request->email)->firstOrFail();
                if ($user->roles->first()->name == 'Siswa') {
                    return redirect(route('siswa.index'));
                } else {
                    session()->flash('fail', 'User Anda Tidak Dapat Digunakan !');
                    return redirect('/login');
                }
            } else {
                session()->flash('fail', 'E-mail / Password Salah !');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi Kesalahan ! Coba Lagi Dalam Beberapa Saat !');
            return redirect()->back();
        }
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string'
        ]);

        try {
            $user = User::firstOrCreate([
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);
            $user->assignRole('Siswa');

            session()->flash('success', 'Berhasil Mendaftar ! Silahkan Cek E-Mail Untuk Verfikasi !');
            return redirect('/register');

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi Kesalahan ! Silahkan Ulangi Kembali Beberapa Saat !');
            return redirect()->back();
        }
    }
}
