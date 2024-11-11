<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\RuleDefinationController;
use App\Http\Controllers\TransactionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/users", function () {
    $users = User::all("name", "email");
    return $users;
})->name("users");

//auth routes
Route::get("/", function () {
    return view("login");
})
    ->middleware("guest")
    ->name("login");

Route::post("/login", [LoginController::class, "authenticate"])
    ->middleware("guest")
    ->name("postLogin");

//dashboard routes

Route::middleware("auth")->group(function () {
    Route::get("/logout", [LoginController::class, "logout"])->name("logout");

    Route::get("/dashboard", function () {
        return view("dashboard");
    })->name("dashboard");

    Route::post("/transactions", [
        TransactionController::class,
        "transfer",
    ])->name("tranfer");

    Route::get("/rules", [RuleDefinationController::class, "create"])->name(
        "rules"
    );

    Route::post("/rules", [RuleDefinationController::class, "store"])->name(
        "rule.store"
    );

    Route::delete("/rules", [RuleDefinationController::class, "destroy"])->name(
        "rule.destroy"
    );
});
