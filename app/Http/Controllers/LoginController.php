<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
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

            Log::create([
                "user_id" => $user->id,
                "activity_type" => "login",
                "login" => [
                    "transaction" => [
                        "ip_address" => $request->ip(),
                        "location" => "Dhaka, BD",
                    ],
                ],
            ]);

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

        Log::create([
            "user_id" => $user->id,
            "activity_type" => "logout",
            "login" => [
                "transaction" => [
                    "ip_address" => $request->ip(),
                    "location" => "Dhaka, BD",
                ],
            ],
        ]);

        $user->logout();

        $request->session()->invalidate();

        return redirect("/");
    }
}
