<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid login credentials.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasPermission('fees')) {
            return redirect()->route('admin.fees.index');
        }
        if ($user->hasPermission('certificates')) {
            return redirect()->route('admin.documents.index');
        }
        if ($user->hasPermission('report_cards') || $user->hasPermission('results')) {
            return redirect()->route('admin.results.index');
        }
        if ($user->hasPermission('attendance')) {
            return redirect()->route('admin.attendance.index');
        }
        if ($user->hasPermission('homework')) {
            return redirect()->route('admin.homework.index');
        }
        if ($user->hasPermission('students')) {
            return redirect()->route('admin.students.index');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login')->withErrors(['email' => 'No management permission has been assigned to this account.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
