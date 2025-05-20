<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\ItemController;
use App\Http\Controllers\Web\OutletController;
use App\Http\Controllers\Web\PurchaseController;
use App\Http\Controllers\Web\SaleController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UnitController;
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

        Route::group(['prefix' => 'unit'], function () {
            Route::post('/', [UnitController::class, 'create']);
            Route::get('/', [UnitController::class, 'findAll']);
            Route::get('/{id}', [UnitController::class, 'findByID']);
            Route::put('/{id}', [UnitController::class, 'patch']);
            Route::delete('/{id}', [UnitController::class, 'delete']);
        });

        Route::group(['prefix' => 'item'], function () {
            Route::post('/', [ItemController::class, 'create']);
            Route::get('/', [ItemController::class, 'findAll']);
            Route::get('/{id}', [ItemController::class, 'findByID']);
            Route::put('/{id}', [ItemController::class, 'patch']);
            Route::delete('/{id}', [ItemController::class, 'delete']);
        });

        Route::group(['prefix' => 'inventory'], function () {
            Route::post('/', [InventoryController::class, 'create']);
            Route::get('/', [InventoryController::class, 'findAll']);
            Route::get('/{id}', [InventoryController::class, 'findByID']);
            Route::put('/{id}', [InventoryController::class, 'patch']);
            Route::delete('/{id}', [InventoryController::class, 'delete']);
        });

        Route::group(['prefix' => 'outlet'], function () {
            Route::post('/', [OutletController::class, 'create']);
            Route::get('/', [OutletController::class, 'findAll']);
            Route::get('/{id}', [OutletController::class, 'findByID']);
            Route::put('/{id}', [OutletController::class, 'patch']);
            Route::delete('/{id}', [OutletController::class, 'delete']);
        });

        Route::group(['prefix' => 'supplier'], function () {
            Route::post('/', [SupplierController::class, 'create']);
            Route::get('/', [SupplierController::class, 'findAll']);
            Route::get('/{id}', [SupplierController::class, 'findByID']);
            Route::put('/{id}', [SupplierController::class, 'patch']);
            Route::delete('/{id}', [SupplierController::class, 'delete']);
        });

        Route::group(['prefix' => 'purchase'], function () {
            Route::post('/', [PurchaseController::class, 'create']);
            Route::get('/', [PurchaseController::class, 'findAll']);
            Route::get('/{id}', [PurchaseController::class, 'findByID']);
            // Route::put('/{id}', [SupplierController::class, 'patch']);
            // Route::delete('/{id}', [SupplierController::class, 'delete']);
        });

        Route::group(['prefix' => 'sale'], function () {
            Route::post('/', [SaleController::class, 'create']);
            Route::get('/', [SaleController::class, 'findAll']);
            Route::get('/{id}', [SaleController::class, 'findByID']);
        });
    });
});
