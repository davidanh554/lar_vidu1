<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('home'));
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $data = $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => bcrypt($data['password']),
        'role'     => 'customer', // Mặc định vai trò là user
    ]);

        $user->sendEmailVerificationNotification();

        Auth::login($user);

       return redirect()->route('verification.notice')
        ->with('success', 'Vui lòng kiểm tra email để xác thực tài khoản.');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
