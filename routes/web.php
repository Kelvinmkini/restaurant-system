<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// ==================== AUTH ROUTES ====================

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// ==================== PROTECTED ROUTES ====================

Route::middleware(['auth'])->group(function () {
    
   // Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chartData');
    Route::get('/dashboard/profit-analysis', [DashboardController::class, 'profitAnalysis'])->name('dashboard.profitAnalysis');
    Route::get('/dashboard/monthly-daily', [DashboardController::class, 'monthlyDailyBreakdown'])->name('dashboard.monthlyDaily');
    Route::get('/dashboard/monthly-summary', [DashboardController::class, 'monthlySummary'])->name('dashboard.monthlySummary');
    Route::get('/dashboard/daily-stats', [DashboardController::class, 'dailyStats'])->name('dashboard.dailyStats');
    Route::get('/dashboard/total-transactions', [DashboardController::class, 'totalTransactions'])->name('dashboard.totalTransactions');

    // ===== Sales Routes =====
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales/report', [SaleController::class, 'report'])->name('sales.report');
    Route::get('/sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/{sale}', [SaleController::class, 'update'])->name('sales.update');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    Route::get('/sales/download', [SaleController::class, 'download'])->name('sales.download');

    // Categories - CRUD
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Food Items - CRUD (zinahitaji category kuwepo kwanza)
    Route::middleware(['categories.exist'])->group(function () {
        Route::get('/food-items', [FoodItemController::class, 'index'])->name('food-items.index');
        Route::get('/food-items/create', [FoodItemController::class, 'create'])->name('food-items.create');
        Route::post('/food-items', [FoodItemController::class, 'store'])->name('food-items.store');
        Route::get('/food-items/{foodItem}/edit', [FoodItemController::class, 'edit'])->name('food-items.edit');
        Route::put('/food-items/{foodItem}', [FoodItemController::class, 'update'])->name('food-items.update');
        Route::delete('/food-items/{foodItem}', [FoodItemController::class, 'destroy'])->name('food-items.destroy');
        Route::patch('/food-items/{id}/restore', [FoodItemController::class, 'restore'])->name('food-items.restore');
        Route::delete('/food-items/{id}/force-delete', [FoodItemController::class, 'forceDelete'])->name('food-items.force-delete');
        Route::patch('/food-items/{foodItem}/toggle-status', [FoodItemController::class, 'toggleStatus'])->name('food-items.toggle-status');
    });
    
    // API routes
    Route::get('/api/chart', [DashboardController::class, 'chartData'])->name('api.chart');
    Route::get('/api/profit', [DashboardController::class, 'profitAnalysis'])->name('api.profit');
    Route::get('/api/monthly-summary', [DashboardController::class, 'monthlySummary'])->name('api.monthly-summary');
    Route::get('/api/daily-stats', [DashboardController::class, 'dailyStats'])->name('api.daily-stats');
    Route::get('/api/monthly-daily', [DashboardController::class, 'monthlyDailyBreakdown'])->name('api.monthly-daily');
});