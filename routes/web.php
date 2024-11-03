<?php

use App\Http\Controllers\LoginController;
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
Route::post("/logout", [LoginController::class, "logout"])->middleware("auth");

//dashboard routes
Route::get("/dashboard", function () {
    return view("dashboard");
})
    ->middleware("auth")
    ->name("dashboard");
