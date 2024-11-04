<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;

class CheckInactiveLogin
{
    public function __invoke(): mixed
    {
        $users = DB::table("users")
            ->where("last_login", "<=", now())
            ->where("rules", "elemMatch", ["type" => "inactivity"])
            ->get();

        $users->each(function ($user) {
            // Find the inactivity rule within the user's rules array
            $inactivityRule = collect($user["rules"])->firstWhere(
                "type",
                "inactivity"
            );

            if ($inactivityRule) {
                $daysInactive =
                    $inactivityRule["conditions"]["days_inactive"] ?? null;

                // Check if user hasn't logged in within the days specified by days_inactive
                if (
                    $daysInactive &&
                    $user["last_login"] <= now()->subDays($daysInactive)
                ) {
                    $actionType = $inactivityRule["action"]["type"] ?? null;

                    if ($actionType === "email") {
                        // Send email notification
                    } elseif ($actionType === "sms") {
                        // Send SMS notification
                    }
                }
            }
        });
    }
}
