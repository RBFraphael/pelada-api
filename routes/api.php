<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GamesController;
use App\Http\Controllers\Api\InvitesController;
use App\Http\Controllers\Api\PlayersController;
use App\Http\Controllers\Api\TeamsController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix("auth")->name("auth.")->group(function () {
    Route::post("login", [AuthController::class, "login"]);
    Route::post("refresh", [AuthController::class, "refresh"]);
    Route::get("me", [AuthController::class, "me"]);
    Route::post("logout", [AuthController::class, "logout"]);
});

Route::middleware('auth:api')->group(function () {
    Route::apiResource('games', GamesController::class);
    Route::apiResource('games/{game}/invites', InvitesController::class);
    Route::apiResource('games/{game}/teams', TeamsController::class);

    Route::apiResource('players', PlayersController::class);
    Route::apiResource('users', UsersController::class);
});

Route::prefix('invites/{invite}')->group(function(){
    Route::post('confirm', [InvitesController::class, 'confirm']);
    Route::post('reject', [InvitesController::class, 'reject']);
});