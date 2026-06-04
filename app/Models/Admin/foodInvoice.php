<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\customer;
use App\Models\Admin\payment_method;

class foodInvoice extends Model
{
    protected $table = 'food_invoices';

    protected $primaryKey = 'foodInvoiceID';

    public $timestamps = false;

    protected $fillable = [
        'customerID',
        'paymentID',
        'orderDate',
        'total',
        'adminID',
    ];

    // =========================================================
    // CUSTOMER
    // =========================================================
    public function customer()
    {
        return $this->belongsTo(customer::class, 'customerID');
    }

    // =========================================================
    // PAYMENT METHOD
    // =========================================================
    public function paymentMethod()
    {
        return $this->belongsTo(payment_method::class, 'paymentID');
    }

    /**
     * FIX TASK 1: Alias cho paymentMethod() — tránh lỗi khi code cũ gọi ->payment
     * Trước đây controller gọi $fi->payment->name nhưng relation tên là paymentMethod()
     * → thêm alias này để backward-compatible với code cũ nếu còn sót
     */
    public function payment()
    {
        return $this->belongsTo(payment_method::class, 'paymentID');
    }

    // =========================================================
    // DETAILS
    // =========================================================
    public function details()
    {
        return $this->hasMany(foodInvoiceDetail::class, 'foodInvoiceID');
    }
}
