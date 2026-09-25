<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Hash;
class LoginController extends Controller
{
    public function showLoginForm()
    {
      return view('admin.login');
    }

    public function locked()
    {
        if(!session('lock-expires-at')){
            return redirect('admin.home');
        }

        if(session('lock-expires-at') > now()){
            return redirect('admin.home');
        }

        return view('auth.locked');
    }

    public function unlock(Request $request)
    {
        $check = Hash::check($request->input('password'), $request->user()->password);

        if(!$check){
            return redirect()->route('admin.locked')->withErrors([
                'Your password does not match your profile.'
            ]);
        }

        session(['lock-expires-at' => now()->addMinutes($request->user()->getLockoutTime())]);

        return redirect('admin.home');
    }
}
