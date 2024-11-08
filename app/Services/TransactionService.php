<?php

namespace App\Services;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TransactionService
{
    /**
     * Evaluate user rules based on transaction activity.
     */
    public function evaluateRules($userId, $transactionAmount)
    {
        $db = DB::connection("mongodb");

        // Fetch the user with rules set for transaction threshold or transaction limit
        $user = $db
            ->table("users")
            ->where("_id", $userId)
            ->where(function ($query) use ($transactionAmount) {
                $query
                    ->orWhere("rules", "elemMatch", [
                        "type" => "transaction_threshold",
                        "conditions.min_transaction_amount" => [
                            '$lte' => $transactionAmount,
                        ],
                    ])
                    ->orWhere("rules", "elemMatch", [
                        "type" => "transaction_limit",
                    ]);
            })
            ->first();

        if ($user) {
            // Check for transaction threshold rule
            $this->checkTransactionThresholdRule($user, $transactionAmount);

            // Check for transaction limit rule
            $this->checkTransactionLimitRule($user, $userId);
        }
    }

    /**
     * Check transaction threshold rule and send notification if applicable.
     */
    private function checkTransactionThresholdRule($user, $transactionAmount)
    {
        $thresholdRule = collect($user["rules"])->firstWhere(
            "type",
            "transaction_threshold"
        );

        if (
            $thresholdRule &&
            $transactionAmount >=
                $thresholdRule["conditions"]["min_transaction_amount"]
        ) {
            $actionType = $thresholdRule["action"]["type"] ?? null;
            $this->triggerNotification(
                $user,
                $actionType,
                "transaction_threshold"
            );
        }
    }

    /**
     * Check transaction limit rule and send notification if applicable.
     */
    private function checkTransactionLimitRule($user, $userId)
    {
        $limitRule = collect($user["rules"])->firstWhere(
            "type",
            "transaction_limit"
        );

        if ($limitRule) {
            $totalCountLimit =
                $limitRule["conditions"]["transaction_count"] ?? null;

            // Count transactions for the current day
            $transactionCount = DB::connection("mongodb")
                ->table("transactions")
                ->where("user_id", $userId)
                ->whereDate("created_at", Carbon::today())
                ->count();

            if ($totalCountLimit && $transactionCount >= $totalCountLimit) {
                $actionType = $limitRule["action"]["type"] ?? null;
                $this->triggerNotification(
                    $user,
                    $actionType,
                    "transaction_limit"
                );
            }
        }
    }

    /**
     * Trigger notification based on action type.
     */
    private function triggerNotification($user, $actionType, $ruleType)
    {
        if ($actionType === "email") {
            Notification::send();
        } elseif ($actionType === "sms") {
            // Send SMS notification
        }
    }
}
