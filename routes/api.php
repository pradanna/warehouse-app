<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Middleware\JWTVerify;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => [JWTVerify::class]], function () {
        Route::group(['prefix' => 'category'], function () {
            Route::post('/', [CategoryController::class, 'create']);
        });
    });
});
