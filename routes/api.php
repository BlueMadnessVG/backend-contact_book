<?php

use App\Http\Controllers\Api\loginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\contactController;

Route::post('/register', [loginController::class, 'register']);
Route::post('/login', [loginController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/contact', [contactController::class, 'index']);
    Route::get('/contact/{id}', [contactController::class,'show']);
    Route::get('/contact/search/{query}', [contactController::class,'search']);
    Route::post('/contact', [contactController::class,'store']);
    Route::patch('/contact/{id}', [contactController::class,'updatePartial']);
    Route::delete('/contact/{id}', [contactController::class,'destroy']);
});
