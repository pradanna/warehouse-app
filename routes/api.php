<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CashFlowController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CreditController;
use App\Http\Controllers\Web\DebtController;
use App\Http\Controllers\Web\ExpenseCategoryController;
use App\Http\Controllers\Web\InventoryAdjustmentController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\InventoryMovementController;
use App\Http\Controllers\Web\ItemController;
use App\Http\Controllers\Web\MaterialCategoryController;
use App\Http\Controllers\Web\OutletController;
use App\Http\Controllers\Web\OutletExpenseController;
use App\Http\Controllers\Web\OutletIncomeController;
use App\Http\Controllers\Web\PurchaseController;
use App\Http\Controllers\Web\PurchasePaymentController;
use App\Http\Controllers\Web\SaleController;
use App\Http\Controllers\Web\SalePaymentController;
use App\Http\Controllers\Web\StaffController;
use App\Http\Controllers\Web\SummaryController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UnitController;
use App\Http\Middleware\JWTVerify;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::post('refresh-token', [AuthController::class, 'refreshToken']);

    Route::post('/refresh', [AuthController::class, 'refresh']);


    Route::group(['middleware' => [JWTVerify::class]], function () {
        Route::group(['prefix' => 'staff'], function () {
            Route::post('/', [StaffController::class, 'create']);
            Route::get('/', [StaffController::class, 'findAll']);
            Route::get('/{id}', [StaffController::class, 'findByID']);
            Route::put('/{id}', [StaffController::class, 'patch']);
            Route::delete('/{id}', [StaffController::class, 'delete']);
        });

        Route::group(['prefix' => 'category'], function () {
            Route::post('/', [CategoryController::class, 'create']);
            Route::get('/', [CategoryController::class, 'findAll']);
            Route::get('/{id}', [CategoryController::class, 'findByID']);
            Route::put('/{id}', [CategoryController::class, 'patch']);
            Route::delete('/{id}', [CategoryController::class, 'delete']);
        });

        Route::group(['prefix' => 'expense-category'], function () {
            Route::post('/', [ExpenseCategoryController::class, 'create']);
            Route::get('/', [ExpenseCategoryController::class, 'findAll']);
            Route::get('/{id}', [ExpenseCategoryController::class, 'findByID']);
            Route::put('/{id}', [ExpenseCategoryController::class, 'patch']);
            Route::delete('/{id}', [ExpenseCategoryController::class, 'delete']);
        });

        Route::group(['prefix' => 'material-category'], function () {
            Route::post('/', [MaterialCategoryController::class, 'create']);
            Route::get('/', [MaterialCategoryController::class, 'findAll']);
            Route::get('/{id}', [MaterialCategoryController::class, 'findByID']);
            Route::put('/{id}', [MaterialCategoryController::class, 'patch']);
            Route::delete('/{id}', [MaterialCategoryController::class, 'delete']);
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
            Route::get('/{sku}/sku', [InventoryController::class, 'findBySku']);
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
            Route::post('/{id}/payment', [PurchaseController::class, 'payment']);
        });

        Route::group(['prefix' => 'purchase-payment'], function () {
            Route::post('/', [PurchasePaymentController::class, 'create']);
            Route::get('/', [PurchasePaymentController::class, 'findAll']);
            Route::get('/{id}', [PurchasePaymentController::class, 'findByID']);
            Route::post('/{id}/evidence', [PurchasePaymentController::class, 'uploadEvidence']);
        });

        Route::group(['prefix' => 'sale'], function () {
            Route::post('/', [SaleController::class, 'create']);
            Route::get('/', [SaleController::class, 'findAll']);
            Route::get('/{id}', [SaleController::class, 'findByID']);
        });

        Route::group(['prefix' => 'sale-payment'], function () {
            Route::post('/', [SalePaymentController::class, 'create']);
            Route::get('/', [SalePaymentController::class, 'findAll']);
            Route::get('/{id}', [SalePaymentController::class, 'findByID']);
            Route::post('/{id}/evidence', [SalePaymentController::class, 'uploadEvidence']);
        });

        Route::group(['prefix' => 'inventory-adjustment'], function () {
            Route::post('/', [InventoryAdjustmentController::class, 'create']);
            Route::get('/', [InventoryAdjustmentController::class, 'findAll']);
            Route::get('/{id}', [InventoryAdjustmentController::class, 'findByID']);
        });

        Route::group(['prefix' => 'inventory-movement'], function () {
            Route::get('/', [InventoryMovementController::class, 'findAll']);
            Route::get('/{id}', [InventoryMovementController::class, 'findByID']);
        });

        Route::group(['prefix' => 'summary'], function () {
            Route::get('/purchase', [SummaryController::class, 'purchase']);
            Route::get('/sale', [SummaryController::class, 'sale']);
            Route::get('/debt', [SummaryController::class, 'debt']);
            Route::get('/credit', [SummaryController::class, 'credit']);
            Route::get('/inventory-movement', [SummaryController::class, 'inventoryMovement']);
            Route::get('/outlet-general-ledger', [SummaryController::class, 'outletGeneralLedger']);
            Route::get('/cash-flow', [SummaryController::class, 'cashFlow']);
        });

        Route::group(['prefix' => 'debt'], function () {
            Route::get('/', [DebtController::class, 'findAll']);
            Route::get('/{id}', [DebtController::class, 'findByID']);
        });

        Route::group(['prefix' => 'credit'], function () {
            Route::get('/', [CreditController::class, 'findAll']);
            Route::get('/{id}', [CreditController::class, 'findByID']);
        });

        Route::group(['prefix' => 'cash-flow'], function () {
            Route::get('/', [CashFlowController::class, 'findAll']);
        });

        Route::group(['prefix' => 'outlet-income'], function () {
            Route::get('/', [OutletIncomeController::class, 'findAll']);
            Route::post('/', [OutletIncomeController::class, 'create']);
            Route::get('/{id}', [OutletIncomeController::class, 'findByID']);
            Route::put('/{id}', [OutletIncomeController::class, 'update']);
            Route::put('/{id}/mutation', [OutletIncomeController::class, 'updateMutation']);
        });

        Route::group(['prefix' => 'outlet-expense'], function () {
            Route::get('/', [OutletExpenseController::class, 'findAll']);
            Route::post('/', [OutletExpenseController::class, 'create']);
            Route::get('/{id}', [OutletExpenseController::class, 'findByID']);
            Route::put('/{id}', [OutletExpenseController::class, 'patch']);
            Route::delete('/{id}', [OutletExpenseController::class, 'delete']);
        });
    });
});
