<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }
    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
        ]);

        $user = User::where("email", $credentials)->firstOrFail();

        if (Auth::login($user)) {
            $request->session()->regenerate();

            //cache last login
            $user->update(["last_login" => now()]);

            $this->logService->logLogin($user->id);

            return redirect()->intended("dashboard");
        }

        return back()
            ->withErrors([
                "email" => "The provided credentials do not match our records.",
            ])
            ->onlyInput("email");
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $this->logService->logLogout($user->id);

        $user->logout();

        $request->session()->invalidate();

        return redirect("/");
    }
}
