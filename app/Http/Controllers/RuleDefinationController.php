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
    protected $db;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
        $this->db = DB::connection("mongodb");
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
    /**
     * Store a newly created rule.
     */
    public function store(RuleDefinationRequest $request)
    {
        $userId = Auth::id();

        $condition = $this->mapConditions(
            $request->type,
            $request->input("conditions")
        );

        DB::transaction(function () use ($userId, $request, $condition) {
            $this->removeExistingRule($userId, $request->type);
            $this->addNewRule($userId, $request, $condition);
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
     * Remove the specified rule.
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
        $this->removeExistingRule($userId, $request->type);
        $this->logService->logCustomRuleDeleted($userId, $request->type);

        return back()->with("success", "Rule removed");
    }

    /**
     * Map conditions based on rule type.
     */
    private function mapConditions($ruleType, $conditions)
    {
        return match ($ruleType) {
            "inactivity" => ["days_inactive" => $conditions["days_inactive"]],
            "transaction_threshold" => [
                "min_transaction_amount" =>
                    $conditions["min_transaction_amount"],
            ],
            "transaction_limit" => [
                "transaction_count" => $conditions["transaction_count"],
            ],
        };
    }

    /**
     * Remove existing rule of the same type.
     */
    private function removeExistingRule($userId, $ruleType)
    {
        $this->db
            ->getCollection("users")
            ->updateOne(
                ["_id" => $userId],
                ['$pull' => ["rules" => ["type" => $ruleType]]]
            );
    }

    /**
     * Add new rule for the user.
     */
    private function addNewRule($userId, $request, $condition)
    {
        $this->db->getCollection("users")->updateOne(
            ["_id" => $userId],
            [
                '$push' => [
                    "rules" => [
                        "name" => $request->name,
                        "type" => $request->type,
                        "conditions" => $condition,
                        "action" => [
                            "type" => $request->input("action.type"),
                            "priority" => $request->input("action.priority"),
                        ],
                    ],
                ],
            ]
        );
    }
}
