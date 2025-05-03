<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\ItemController;
use App\Http\Middleware\JWTVerify;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::group(['middleware' => [JWTVerify::class]], function () {
        Route::group(['prefix' => 'category'], function () {
            Route::post('/', [CategoryController::class, 'create']);
            Route::get('/', [CategoryController::class, 'findAll']);
            Route::get('/{id}', [CategoryController::class, 'findByID']);
            Route::put('/{id}', [CategoryController::class, 'patch']);
            Route::delete('/{id}', [CategoryController::class, 'delete']);
        });

        Route::group(['prefix' => 'item'], function () {
            Route::post('/', [ItemController::class, 'create']);
            Route::get('/', [ItemController::class, 'findAll']);
            Route::get('/{id}', [ItemController::class, 'findByID']);
            Route::put('/{id}', [ItemController::class, 'patch']);
            Route::delete('/{id}', [ItemController::class, 'delete']);
        });
    });
});
