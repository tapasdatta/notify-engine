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
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            "email" => ["required", "email"],
        ]);

        $user = User::where("email", $request->input("email"))->firstOrFail();

        if (!Auth::loginUsingId($user->id)) {
            return back()
                ->withErrors([
                    "email" =>
                        "The provided credentials do not match our records.",
                ])
                ->onlyInput("email");
        }

        $request->session()->regenerate();
        $this->cacheLastLogin($user);
        $this->logService->logLogin($user->id);

        return redirect()->intended("dashboard");
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        $this->logService->logLogout($userId);

        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route("login");
    }

    /**
     * Cache the user's last login time.
     */
    private function cacheLastLogin(User $user)
    {
        $user->last_login = now();
        $user->save();
    }
}
