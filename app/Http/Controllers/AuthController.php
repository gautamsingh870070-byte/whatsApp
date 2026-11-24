<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('jwt_token')) {
            try {
                $user = Auth::guard('api')->setToken(session('jwt_token'))->authenticate();
                if ($user) {
                    return redirect($user->getHomeRoute());
                }
            } catch (\Exception $e) {
                // Invalid token, continue to login view
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Invalid email or password.'
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        session([
            'jwt_token' => $token,
        ]);

        return redirect()->intended(Auth::guard('api')->user()->getHomeRoute());
    }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();
        } catch (\Exception $e) {
            // Token already expired/invalid
        }
        session()->forget('jwt_token');

        return redirect('/login');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        $user->update([
            'password' => \Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password changed successfully!');
    }
}
