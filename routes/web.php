<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TransactionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view("welcome");
});

Route::get("/users", function () {
    $users = User::all("name", "email");
    return $users;
});

//auth routes
Route::get("/login", function () {
    return view("login");
})
    ->middleware("guest")
    ->name("login");

Route::post("/login", [LoginController::class, "authenticate"])->middleware(
    "guest"
);

//dashboard routes

Route::middleware("auth")->group(function () {
    Route::post("/logout", [LoginController::class, "logout"])->name("logout");

    Route::get("/dashboard", function () {
        return view("dashboard");
    })->name("dashboard");

    Route::post("/transactions", [
        TransactionController::class,
        "transfer",
    ])->name("tranfer");
});
