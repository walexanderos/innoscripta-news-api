<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function(){

    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::prefix('articles')->controller(ArticleController::class)->group(function(){
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

    Route::prefix('user')->controller(UserController::class)->group(function(){
        Route::post('/preferences', 'setPreferences');
        Route::get('/preferences', 'getPreferences');
        Route::get('/personalized-feed', 'personalizedNewsFeed');
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

});
