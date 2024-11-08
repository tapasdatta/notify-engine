<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTransaction;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    protected $logService;
    protected $db;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
        $this->db = DB::connection("mongodb");
    }

    /**
     * Transfer fund.
     */
    public function transfer(Request $request)
    {
        $request->validate([
            "email" => ["required", "email", "exists:users,email"],
            "amt" => ["required", "integer", "min:1"],
        ]);

        $db = DB::connection("mongodb");
        $userId = Auth::id();
        $amount = $request->amt;

        DB::transaction(function () use ($db, $userId, $amount, $request) {
            // Deduct amount from authenticated user's balance if sufficient
            $this->deductBalance($userId, $amount);
            // Add amount to recipient's balance
            $this->creditBalance($request->email, $amount);
        });

        //log the transaction
        $this->logService->logTransaction($userId, $amount);
        //Run the rules if the user has
        ProcessTransaction::dispatch($userId, $amount);

        // return back()->with("success", "Transfer successful");
    }

    //Deduct available balance from sender
    private function deductBalance($userId, int $amount)
    {
        $result = $this->db
            ->table("users")
            ->where("id", $userId)
            ->where("balance.available", ">=", $amount)
            ->decrement("balance.available", $amount);

        // Check if the balance update succeeded
        if ($result == 0) {
            return back()->with("error", "Insufficient funds!");
        }
    }

    //Credit balance from sender
    private function creditBalance($email, int $amount)
    {
        $result = $this->db
            ->table("users")
            ->where("email", $email)
            ->where("balance.available", ">=", $amount)
            ->increment("balance.available", $amount);

        // Check if the balance update succeeded
        if ($result == 0) {
            throw ValidationException::withMessages([
                "amt" => "Insufficient funds!",
            ]);
        }
    }
}
