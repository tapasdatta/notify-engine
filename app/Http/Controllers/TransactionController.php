<?php

namespace App\Http\Controllers;

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
        return DB::transaction(function () use ($request) {
            $db = DB::connection("mongodb");

            $result = $db->getCollection("users")->updateOne(
                [
                    "_id" => Auth()->id(),
                    "balance.$.available" => ['$gte' => $request->amt],
                ],
                [
                    '$inc' => ['balance.$.available' => -$request->amt],
                ]
            );

            throw_unless(
                $result["updateExisting"],
                ValidationException::class,
                "insufficient fund!"
            );

            $db->getCollection("users")->updateOne(
                [
                    "email" => $request->email,
                ],
                [
                    '$inc' => ['balance.$.available' => $request->amt],
                ]
            );

            //Todo: log fund transfer data
        }, 3);

        return "fund transfer successfull!";
    }
}
