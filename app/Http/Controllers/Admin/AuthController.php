<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin(): View
    {
        return view('admin.login');
    }

    /**
     * Authenticate the admin using the configured password.
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $configuredPassword = (string) config('app.admin_password');
        $hashedPassword = Hash::isHashed($configuredPassword)
            ? $configuredPassword
            : Hash::make($configuredPassword);

        if (! Hash::check($credentials['password'], $hashedPassword)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Senha incorreta.'], Response::HTTP_UNAUTHORIZED);
            }

            return back()->withErrors(['password' => 'Senha incorreta.'])->withInput();
        }

        $request->session()->regenerate();
        session(['admin_authenticated' => true]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('admin.dashboard');
    }

    /**
     * Invalidate the admin session.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Sessão encerrada.');
    }
}
