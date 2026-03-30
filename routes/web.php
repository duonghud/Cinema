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
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
});

Route::prefix('admins')->namespace('App\Http\Controllers\Admin')->group(function () {
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
Route::resource('/auth/customerRegister', CustomerAuthController::class);

Route::get('/login', function () {
    return view('auth.customerLogin');
})->name('login');
