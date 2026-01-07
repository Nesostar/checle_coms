<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Show login page
    public function login()
    {
        return view('login');
    }

    // Handle login logic
    public function authenticate(Request $request)
{
    $request->validate([
        'username' => 'required|string', // email OR name
        'password' => 'required|string',
        'role'     => 'required|in:admin,staff',
    ]);

    // Allow login via email OR name
    $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL)
        ? 'email'
        : 'name';

    $credentials = [
        $loginField => $request->username,
        'password'  => $request->password,
        'role'      => $request->role,
    ];

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        if (Auth::user()->role === 'admin') {
            return redirect()
                ->route('admin.dashboard.index')
                ->with('success', 'Welcome Admin!');
        }

        // staff = cashier
        return redirect()
            ->route('cashier.dashboard.index')
            ->with('success', 'Welcome Cashier!');
    }

    return back()->withErrors([
        'login' => 'Invalid login credentials.',
    ]);
}

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out.');
    }
}
