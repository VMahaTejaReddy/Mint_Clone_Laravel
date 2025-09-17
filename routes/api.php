<?php
// use Illuminate\Support\Facades\Route;
// use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\AccountController;
// use App\Http\Controllers\TransactionController;
// use App\Http\Controllers\CategoryController;
// use App\Http\Controllers\BudgetController;
// use App\Http\Controllers\BillController;
// use App\Http\Controllers\GoalController;

// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class,'login']);

// Route::middleware('auth:api')->group(function () {
//     Route::get('/me', [AuthController::class, 'me']);
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/refresh', [AuthController::class, 'refresh']);

//     Route::get('/profile', [UserController::class, 'profile']);
//     Route::put('/user', [UserController::class, 'update']); // Route for updating the user
//     Route::delete('/user', [UserController::class, 'destroy']); // Route for deleting the user

//     Route::apiResource('users', UserController::class);
//     Route::apiResource('accounts', AccountController::class);
//     Route::apiResource('transactions', TransactionController::class);
//     Route::apiResource('categories', CategoryController::class);
//     Route::apiResource('budgets', BudgetController::class);
//     Route::apiResource('bills', BillController::class);
//     Route::apiResource('goals', GoalController::class);



//     Route::get('/chart/spending-by-category', [App\Http\Controllers\DashboardController::class, 'spendingByCategory']);
// });

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\NotificationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/refresh', [AuthController::class, 'refresh']);

    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/user', [UserController::class, 'update']);
    Route::delete('/user', [UserController::class, 'destroy']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::apiResource('bills', BillController::class);
    Route::apiResource('goals', GoalController::class);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

    Route::get('/chart/spending-by-category', [App\Http\Controllers\DashboardController::class, 'spendingByCategory']);
});

