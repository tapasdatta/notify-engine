<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RuleDefinationController extends Controller
{
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
    public function store(Request $request)
    {
        $request->validate([
            "name" => ["required", "string", "max:255"],
            "type" => [
                "required",
                "string",
                "in:inactivity,transaction_threshold,transaction_limit",
            ],

            // Conditions based on the rule type
            "conditions.days_inactive" => [
                "required_if:type,inactivity",
                "integer",
                "min:1",
            ],
            "conditions.min_transaction_amount" => [
                "required_if:type,transaction_threshold",
                "numeric",
                "min:1",
            ],
            "conditions.transaction_count" => [
                "required_if:type,transaction_limit",
                "numeric",
                "min:2",
            ],

            // Action fields
            "action.type" => ["required", "string", "in:email,sms"],
            "action.priority" => ["required", "string", "min:1", "max:3"],
        ]);

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
        }, 2);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rule $rule)
    {
        //
    }
}
