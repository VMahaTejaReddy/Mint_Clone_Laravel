<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\DashboardController;

Route::middleware('auth:api')->get('/dashboard-data', [DashboardController::class, 'getData']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh', [AuthController::class, 'refresh']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::apiResource('bills', BillController::class);
    Route::apiResource('goals', GoalController::class);
});