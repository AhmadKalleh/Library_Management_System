<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Book\BookController;
use App\Http\Controllers\Api\Borrow\BorrowController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\User\UserController;
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
    Route::get('books/filter',   [BookController::class, 'filter']);

    // Borrows - User can see only their borrows but admin can see all borrows
    Route::get('borrows',        [BorrowController::class, 'index']);

    // User can create borrow request but admin cannot create borrow request
    Route::post('borrows', [BorrowController::class, 'borrow_request']);
    //Categorys  public for user and admin but create update delete for admin only
    Route::get('categories',     [CategoryController::class, 'index']);

    // User Profile
    Route::get('profile', [UserController::class, 'profile']);
    Route::post('profile/{user}', [UserController::class, 'update_user']);
    // Admin Only
    Route::middleware('can:is-admin')->group(function ()
    {

        // Books
        Route::get('books/show', [BookController::class, 'show_book']);
        Route::post('books/create',          [BookController::class, 'create_book']);
        Route::post('books/update',    [BookController::class, 'update_book']);
        Route::delete('books/delete', [BookController::class, 'delete_book']);


        // Category
        Route::post('categories/create',  [CategoryController::class, 'create_category']);
        Route::post('categories/update',  [CategoryController::class, 'update_category']);
        Route::delete('categories/delete',[CategoryController::class, 'delete_category']);

        // Borrows
        Route::post('borrows/confirm', [BorrowController::class, 'confirm_receive']);
        Route::post('borrows/return',  [BorrowController::class, 'return_book']);

        // user management
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/show-user', [UserController::class, 'show_user']);
        Route::post('users', [UserController::class, 'create_user']);
        Route::delete('users/delete-user', [UserController::class, 'delete_user']);
        Route::get('statistics', [UserController::class, 'dashboard_statistics']);
        Route::post('users/activate',     [UserController::class, 'activate']);
        Route::post('users/ban',          [UserController::class, 'ban']);

    });
});
