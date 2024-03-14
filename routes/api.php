<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    AuthController
};
use App\Http\Middleware\JWTChecker;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/

Route::post('/signup', [UserController::class, 'signUp']);
Route::post('/signin', [AuthController::class, 'signIn']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

Route::middleware(JWTChecker::class)->group(function(){
    //
    Route::apiResource('/users', UserController::class);
});
