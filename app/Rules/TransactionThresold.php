<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;

class TransactionThresold
{
    /**
     * Create a new class instance.
     */
    public function __invoke()
    {
        $users = DB::table("users")
            ->where("rules", "elemMatch", ["type" => "transaction_threshold"])
            ->get();
    }
}
