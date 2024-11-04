<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
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
            $result = $db->getCollection("users")->updateOne(
                [
                    "_id" => $userId,
                    "balance.available" => ['$gte' => $amount],
                ],
                [
                    '$inc' => ["balance.available" => -$amount],
                ]
            );

            // Check if the balance update succeeded
            if ($result->getModifiedCount() === 0) {
                throw ValidationException::withMessages([
                    "amt" => "Insufficient funds!",
                ]);
            }

            // Add amount to recipient's balance
            $db->getCollection("users")->updateOne(
                [
                    "email" => $request->email,
                ],
                [
                    '$inc' => ["balance.available" => $amount],
                ]
            );

            Log::create([
                "user_id" => $userId,
                "activity_type" => "transaction",
                "activity_details" => [
                    "transaction" => [
                        "transaction_id" => $userId,
                        "amount" => $amount,
                        "currency" => "USD",
                        "transaction_type" => "debit",
                    ],
                ],
            ]);
        }, 3);

        return back()->with("success", "Transfer successful");
    }
}
