<?php

use App\Http\Controllers\Admin\adminController;
use App\Http\Controllers\Admin\ageRatingController;
use App\Http\Controllers\Admin\customerController;
use App\Http\Controllers\LoginReigster\CustomerAuthController;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\foodController;
use App\Http\Controllers\Admin\genreController;
use App\Http\Controllers\Admin\paymentMethodController;
use App\Http\Controllers\Admin\screeningTypeController;
use App\Http\Controllers\Admin\seatTypeController;
use App\Http\Controllers\Admin\studioController;
use App\Http\Controllers\Admin\movieController;
use App\Http\Controllers\Admin\screeningRoomController;
use App\Http\Controllers\Admin\foodInvoiceController;
use App\Http\Controllers\Admin\foodInvoiceDetailController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Admin\ticketController;
use App\Http\Controllers\Admin\showTimeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginReigster\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::middleware('role:admin')->group(function () {
    Route::redirect('/admin', '/admins/dashboard')->name('admin.home');
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

Route::prefix('admins')->namespace('App\Http\Controllers\Admin')->middleware('role:admin')->group(function () {
    Route::resource('dashboard', DashBoardController::class);
    Route::resource('admin', AdminController::class);
    Route::resource('customer', CustomerController::class);
    Route::resource('paymentMethod', PaymentMethodController::class);
    Route::resource('food', FoodController::class);
    Route::resource('foodInvoice', foodInvoiceController::class);
    Route::resource('foodInvoiceDetail', FoodInvoiceDetailController::class);
    Route::resource('genre', GenreController::class);
    Route::post('seat/generate', [SeatController::class, 'generate'])->name('seat.generate');
    Route::resource('studio', StudioController::class);
    Route::resource('ageRating', AgeRatingController::class);
    Route::resource('movies', MovieController::class);
    Route::resource('screeningRoom', ScreeningRoomController::class);
    Route::resource('screenType', ScreeningTypeController::class);
    Route::resource('seat', SeatController::class);
    Route::resource('seatType', SeatTypeController::class);
    Route::resource('ticket', ticketController::class);
    Route::resource('showTime', showTimeController::class);
});
// routes/web.php
Route::get('/movies/{movie}', [MovieController::class, 'show'])->name('movies.show');
// // Hiển thị form đăng ký
// Route::get('/auth/customerRegister', [CustomerAuthController::class, 'create'])
//     ->name('customerRegister.form');

// // Xử lý dữ liệu đăng ký
// Route::post('/auth/customerRegister', [CustomerAuthController::class, 'store'])
//     ->name('customerRegister');

Route::get('/login', function () {
    return view('auth.customerLogin');
})->name('login');