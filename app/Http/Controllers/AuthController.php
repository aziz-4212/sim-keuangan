<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.vlogin');
    }

    public function loginCheck(Request $request)
    {
        $pass = $request->USPASS;
        $user = User::where('USERLOGNM', $request->USLOGNM)
            ->whereHas('user_log', function ($query) use ($pass) {
                $query->where('USPASS', $pass);
            })
            ->first();

        if ($user) {
            Auth::login($user);
            return redirect('/');
        }

        return redirect('/login')->with('error', 'Username atau password salah.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function contoh()
    {
        return view('template-print.scanner');

    }
}
