<?php

use Illuminate\Support\Facades\Route;

// ================== CONTROLLERS ==================
use App\Http\Controllers\HomeController;
use App\Http\Controllers\showController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\SystemPaymentController;
use App\Http\Controllers\BookingHistoryController;

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
use App\Http\Controllers\VnpayController;
use App\Http\Controllers\MomoController;



// ================== CLIENT ==================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/show', [showController::class, 'index'])->name('show');
Route::view('/contact', 'system.contact')->name('contact');
Route::view('/ticket-price', 'system.ticketprice')->name('ticket.price');
Route::get('/profile', function () {
    $customer = session('customer');
    return view('system.profile', compact('customer'));
})->name('customer.profile');

Route::get('/vnpay/return', [VnpayController::class, 'vnpayReturn'])
    ->name('vnpay.return');

Route::get('/momo/return', [MomoController::class, 'momoReturn'])
    ->name('momo.return');
Route::post('/momo/notify', [MomoController::class, 'momoNotify'])
    ->name('momo.notify');
    

// ================== AUTH CUSTOMER ==================
Route::prefix('customer')->group(function () {
    Route::get('/register', [CustomerAuthController::class, 'showRegister'])
        ->name('customer.register.form');
    Route::post('/register', [CustomerAuthController::class, 'register'])
        ->name('customer.register');
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])
        ->name('auth.customerLogin');
    Route::post('/login', [CustomerAuthController::class, 'customerLogin'])
        ->name('customer.login.post');
    Route::post('/logout', [CustomerAuthController::class, 'logout'])
        ->name('customer.logout');
});

// ================== AUTH ADMIN ==================
Route::prefix('admins')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
        ->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->name('admin.login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');
});

// ================== ADMIN ==================
Route::middleware('admin.auth')->group(function () {
    Route::redirect('/admin', '/admins/dashboard')->name('admin.home');
});

Route::prefix('admins')->middleware('admin.auth')->group(function () {

    // Dashboard
    Route::resource('dashboard', DashBoardController::class);

    // Revenue reports
    Route::get('reports/revenue-by-month', [DashBoardController::class, 'revenueByMonth'])
        ->name('reports.revenue.month');
    Route::get('reports/revenue-by-day', [DashBoardController::class, 'revenueByDay'])
        ->name('reports.revenue.day');

    Route::get('/reports/invoices-by-period', [DashBoardController::class, 'invoicesByPeriod'])
        ->name('admins.reports.invoices-by-period');



    // Resources
    Route::resource('admin',             AdminController::class);
    Route::resource('customer',          CustomerController::class);
    Route::resource('paymentMethod',     PaymentMethodController::class);
    Route::resource('food',              FoodController::class);
    Route::resource('foodInvoice',       FoodInvoiceController::class);
    Route::resource('foodInvoiceDetail', FoodInvoiceDetailController::class);
    Route::resource('genre',             GenreController::class);
    Route::resource('studio',            StudioController::class);
    Route::resource('ageRating',         AgeRatingController::class);
    Route::resource('movies',            MovieController::class)->names('admin.movies');
    Route::resource('screeningRoom',     ScreeningRoomController::class);
    Route::resource('screenType',        ScreeningTypeController::class);
    Route::resource('seatType',          SeatTypeController::class);
    Route::resource('ticket',            TicketController::class);
    Route::resource('showTime',          ShowTimeController::class);
    Route::resource('invoices',          InvoiceController::class);

    // Seat AJAX
    Route::get('seat/ajax/{roomID}',         [SeatController::class, 'getSeatsByRoom'])
        ->name('seat.ajax.list');
    Route::post('seat/ajax-add',             [SeatController::class, 'storeAjax'])
        ->name('seat.ajax.add');
    Route::post('seat/ajax-update-type',     [SeatController::class, 'ajaxUpdateType'])
        ->name('seat.ajax.updateType');
    Route::delete('seat/ajax-delete/{id}',   [SeatController::class, 'deleteAjax'])
        ->name('seat.ajax.delete');
    Route::post('seat/update-multiple',      [SeatController::class, 'updateMultiple'])
        ->name('seat.updateMultiple');
    Route::get('seat/edit-multiple',         [SeatController::class, 'editMultiple'])
        ->name('seat.editMultiple');
    Route::post('seat/ajax-swap-type',       [SeatController::class, 'ajaxSwapType'])
        ->name('seat.ajax.swapType');
    Route::post('seat/ajax-convert-couple',  [SeatController::class, 'ajaxConvertCouple'])
        ->name('seat.ajax.convertCouple');
    Route::post('seat/ajax-move-couple',     [SeatController::class, 'ajaxMoveCouple'])
        ->name('seat.ajax.moveCouple');
    Route::post('seat/ajax-swap-couple-type', [SeatController::class, 'ajaxSwapCoupleType'])
        ->name('seat.ajax.swapCoupleType');
    Route::post('seat/ajax-batch-update-type', [SeatController::class, 'ajaxBatchUpdateType'])
        ->name('seat.ajax.batchUpdateType');
    Route::resource('seat', SeatController::class);
});

// ================== MOVIE DETAIL ==================
Route::get('/movies/{movie}', [MovieController::class, 'show'])
    ->name('movies.show');
Route::get('/select-seat/{id}', [SeatController::class, 'selectSeat'])
    ->name('seat.select');

// ================== CUSTOMER MIDDLEWARE ====================
Route::middleware('customer.login')->group(function () {
    Route::get('/member', function () {
        return view('customer.member');
    })->name('customer.member');

    Route::get('/invoice', function () {
        return view('invoice.index');
    })->name('invoice');

    Route::get('/payment', [SystemPaymentController::class, 'index'])
        ->name('payment');
    Route::post('/invoice/confirm', [SystemPaymentController::class, 'confirm'])
        ->name('invoice.confirm');
    Route::post('/payment', [SystemPaymentController::class, 'store'])
        ->name('system.payment');
    Route::get('/payment/success/{invoiceID}', [SystemPaymentController::class, 'success'])
        ->name('system.success');
    Route::get('/booking-history', [BookingHistoryController::class, 'index'])
        ->name('booking.history');
});
