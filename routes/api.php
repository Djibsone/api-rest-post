<?php

use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
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

// créer un lien qui permettra aux clients : React, Vue, Angular, Node, JS

Route::prefix('users')->name('users.')->controller(UserController::class)->group(function() {
    Route::get('/', 'index')->name('index');
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::put('/user-{user}', 'update');
    Route::delete('/user-{user}', 'delete');
    Route::delete('/logout', 'logout');
});

Route::get('/posts', [PostController::class, 'index'])->name('index');

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('posts')->name('posts.')->controller(PostController::class)->group(function() {
        Route::post('/create', 'store');
        Route::put('/post-{post}', 'update');
        Route::delete('/post-{post}', 'delete');
    });
    Route::get('/user', function(Request $request) {
        return $request->user();
    });
});
