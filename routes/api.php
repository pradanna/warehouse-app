<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); // View all categories
    Route::get('/{id}', [CategoryController::class, 'show']); // View single category
    Route::post('/', [CategoryController::class, 'store']); // Create category
    Route::put('/{id}', [CategoryController::class, 'update']); // Edit category
    Route::delete('/{id}', [CategoryController::class, 'destroy']); // Delete category
});
