<?php

namespace App\Services;

use App\Models\Log;
use Illuminate\Http\Request;

class LogService
{
    protected $request;
    /**
     * Create a new class instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function logActivity(
        $userId,
        $activityType,
        array $activityDetails = []
    ) {
        $logData = [
            "user_id" => $userId,
            "activity_type" => $activityType,
            "activity_details" => $activityDetails,
        ];

        // Add login-specific IP and location details for login/logout
        if (in_array($activityType, ["login", "logout"])) {
            $logData["activity_details"]["login"] = [
                "ip_address" => $this->request->ip(),
                "location" => "Dhaka, BD",
            ];
        }

        Log::create($logData);
    }

    public function logLogin($userId)
    {
        $this->logActivity($userId, "login");
    }

    public function logLogout($userId)
    {
        $this->logActivity($userId, "logout");
    }

    public function logCustomRuleDeleted($userId, $ruleType)
    {
        $this->logActivity($userId, $ruleType);
    }

    public function logTransaction(
        $userId,
        $amount,
        $transactionType = "debit",
        $currency = "USD"
    ) {
        $this->logActivity($userId, "transaction", [
            "transaction" => [
                "transaction_id" => $userId, // Assuming userId for simplicity; replace as needed
                "amount" => $amount,
                "currency" => $currency,
                "transaction_type" => $transactionType,
            ],
        ]);
    }

    public function logCustomRule(
        $userId,
        $ruleName,
        $actionType,
        $priority,
        $conditions
    ) {
        $this->logActivity($userId, "custom_rule", [
            "custom_rule" => [
                "rule_name" => $ruleName,
                "action_taken" => $actionType,
                "priority" => $priority,
                "rule_conditions" => $conditions,
            ],
        ]);
    }

    public function logCustomRuleDelete($userId, $ruleType)
    {
        $this->logActivity($userId, "custom_rule_deleted", [
            "custom_rule" => [
                "rule_rtow" => $ruleType,
            ],
        ]);
    }
}
