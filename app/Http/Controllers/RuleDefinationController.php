<?php

namespace App\Http\Controllers;

use App\Http\Requests\RuleDefinationRequest;
use App\Models\User;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RuleDefinationController extends Controller
{
    protected $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RuleDefinationRequest $request)
    {
        $userId = Auth::id();
        $ruleType = $request->type;
        $db = DB::connection("mongodb");

        // Map conditions based on the type
        $condition = match ($ruleType) {
            "inactivity" => [
                "days_inactive" => $request->input("conditions.days_inactive"),
            ],
            "transaction_threshold" => [
                "min_transaction_amount" => $request->input(
                    "conditions.min_transaction_amount"
                ),
            ],
            "transaction_limit" => [
                "transaction_count" => $request->input(
                    "conditions.transaction_count"
                ),
            ],
        };

        DB::transaction(function () use (
            $db,
            $userId,
            $ruleType,
            $request,
            $condition
        ) {
            // Remove existing rule of the same type
            $db->getCollection("users")->updateOne(
                ["_id" => $userId],
                ['$pull' => ["rules" => ["type" => $ruleType]]]
            );

            // Add the new or updated rule
            $db->getCollection("users")->updateOne(
                ["_id" => $userId],
                [
                    '$push' => [
                        "rules" => [
                            "name" => $request->name,
                            "type" => $ruleType,
                            "conditions" => $condition,
                            "action" => [
                                "type" => $request->input("action.type"),
                                "priority" => $request->input(
                                    "action.priority"
                                ),
                            ],
                        ],
                    ],
                ]
            );

            $this->logService->logCustomRule(
                $userId,
                $request->name,
                $request->input("action.type"),
                $request->input("action.priority"),
                $condition
            );
        }, 2);

        return back()->with("success", "Rule created");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $rule, Request $request)
    {
        $request->validate([
            "type" => [
                "required",
                "string",
                "in:inactivity,transaction_threshold,transaction_limit",
            ],
        ]);

        $userId = Auth::id();
        $ruleType = $request->type;
        $db = DB::connection("mongodb");

        // Remove existing rule of the same type
        $db->getCollection("users")->updateOne(
            ["_id" => $userId],
            ['$pull' => ["rules" => ["type" => $ruleType]]]
        );

        $this->logService->logCustomRuleDeleted($userId, $ruleType);

        return back()->with("success", "Rule removed");
    }
}
