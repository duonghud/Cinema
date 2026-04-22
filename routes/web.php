<?php

use Illuminate\Support\Facades\Route;

// ================== CONTROLLERS ==================
use App\Http\Controllers\HomeController;
use App\Http\Controllers\showController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\AdminAuthController;

// Admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\FoodController;
use App\Http\Controllers\Admin\FoodInvoiceController;
use App\Http\Controllers\Admin\FoodInvoiceDetailController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\AgeRatingController;
use App\Http\Controllers\Admin\MovieController;
use App\Http\Controllers\Admin\ScreeningRoomController;
use App\Http\Controllers\Admin\ScreeningTypeController;
use App\Http\Controllers\Admin\SeatController;
use App\Http\Controllers\Admin\SeatTypeController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\ShowTimeController;
use App\Http\Controllers\Admin\InvoiceController;


// ================== CLIENT ==================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/show', [showController::class, 'index'])->name('show');
Route::view('/contact', 'system.contact')->name('contact');
Route::view('/ticket-price', 'system.ticketprice')->name('ticket.price');
Route::view('/profile', 'system.profile')->name('system.profile');
Route::get('/movie', [MovieController::class, 'index'])->name('system.movie');
Route::get('/payment', [PaymentMethodController::class, 'index'])->name('system.payment');

// ================== AUTH CUSTOMER ==================
Route::prefix('customer')->group(function () {

    // register
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])
        ->name('customer.register.form');
    Route::post('/register', [CustomerAuthController::class, 'register'])
        ->name('customer.register');

    // login
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])
        ->name('auth.customerLogin');
    Route::post('/login', [CustomerAuthController::class, 'customerLogin'])
        ->name('customer.login.post');


    // logout
    Route::post('/logout', [CustomerAuthController::class, 'logout'])
        ->name('customer.logout');
});

// ================== AUTH ADMIN ==================


Route::prefix('admins')->group(function () {
    // login form
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
        ->name('admin.login');

    // login submit
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('admin.login.post');

    // logout
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');
});



// ================== ADMIN ==================

Route::middleware('admin.auth')->group(function () {
    Route::redirect('/admin', '/admins/dashboard')->name('admin.home');
});

Route::prefix('admins')->middleware('admin.auth')->group(function () {


    Route::resource('dashboard', DashBoardController::class);
    Route::resource('admin', AdminController::class);
    Route::resource('customer', CustomerController::class);
    Route::resource('paymentMethod', PaymentMethodController::class);
    Route::resource('food', FoodController::class);
    Route::resource('foodInvoice', FoodInvoiceController::class);
    Route::resource('foodInvoiceDetail', FoodInvoiceDetailController::class);
    Route::resource('genre', GenreController::class);

    // ================= SEAT AJAX =================
    Route::get('seat/ajax/{roomID}', [SeatController::class, 'getSeatsByRoom'])
        ->name('seat.ajax.list');

    Route::post('seat/ajax-store', [SeatController::class, 'storeAjax'])
        ->name('seat.ajax.store');

    Route::get('seat/edit-multiple', [SeatController::class, 'editMultiple'])
        ->name('seat.editMultiple');

    Route::post('seat/update-multiple', [SeatController::class, 'updateMultiple'])
        ->name('seat.updateMultiple');
        
    Route::delete('seat/ajax-delete/{id}', [SeatController::class, 'deleteAjax'])
        ->name('seat.ajax.delete');

    Route::post('seat/generate', [SeatController::class, 'generate'])
        ->name('seat.generate');

    Route::resource('studio', StudioController::class);
    Route::resource('ageRating', AgeRatingController::class);
    Route::resource('movies', MovieController::class)
        ->names('admin.movies');
    Route::resource('screeningRoom', ScreeningRoomController::class);
    Route::resource('screenType', ScreeningTypeController::class);
    Route::resource('seat', SeatController::class);
    Route::resource('seatType', SeatTypeController::class);
    Route::resource('ticket', TicketController::class);
    Route::resource('showTime', ShowTimeController::class);
    Route::resource('invoices', InvoiceController::class);
});


// ================== MOVIE DETAIL ==================
Route::get('/movies/{movie}', [MovieController::class, 'show'])
    ->name('movies.show');
Route::get('/select-seat/{id}', [SeatController::class, 'selectSeat'])
    ->name('seat.select');

// ================== Middleware ====================
Route::middleware('customer.login')->group(function () {


    Route::get('/member', function () {
        return view('customer.member');
    })->name('customer.member');

    Route::get('/booking', function () {
        return view('booking.index');
    })->name('booking');

    Route::get('/payment', function () {
        return view('payment.index');
    })->name('payment');
});
