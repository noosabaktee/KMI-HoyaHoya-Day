<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = trim($credentials['username']);

        // Check by username or email
        $user = User::where(function ($q) use ($username) {
            $q->where('txtUsername', $username)
              ->orWhere('txtEmail', $username);
        })->where('bitActive', 1)->first();

        if (!$user || !Hash::check($credentials['password'], $user->txtPassword)) {
            throw ValidationException::withMessages([
                'username' => 'Nama pengguna atau kata sandi tidak valid.',
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        LogActivity::record('LOGIN', "User {$user->txtUsername} ({$user->txtRole}) berhasil masuk sistem.", $user->intUser_ID);

        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogActivity::record('LOGOUT', "User " . Auth::user()->txtUsername . " keluar sistem.");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('umkm.pos');
    }
}

