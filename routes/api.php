<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Book\BookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function ()
{
    Route::post('/register',     [AuthController::class, 'register']);
    Route::post('/login',        [AuthController::class, 'login']);
    Route::post('/verify-code',  [AuthController::class, 'verify_code']);
    Route::post('/resend-code',  [AuthController::class, 'resend_code']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });

});

Route::middleware('auth:sanctum')->group(function () {

    // Public - User + Admin
    Route::get('books',          [BookController::class, 'index']);
    Route::get('books/search',   [BookController::class, 'global_search']);

    // Admin Only
    Route::middleware('can:is-admin')->group(function () {
        Route::post('books/create',          [BookController::class, 'create_book']);
        Route::post('books/update',    [BookController::class, 'update_book']);
        Route::delete('books/delete', [BookController::class, 'delete_book']);
    });
});
