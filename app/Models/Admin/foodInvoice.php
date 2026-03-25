<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Customer;
use App\Models\Admin\Admin;
use App\Models\Admin\payment_method;

class FoodInvoice extends Model
{
    protected $table = 'food_invoices';

    protected $primaryKey = 'foodInvoiceID';

    public $timestamps = false;

    protected $fillable = [
        'orderDate',
        'total',
        'customerID',
        'adminID',
        'paymentID'
    ];

    // customer
    public function customer()
    {
        return $this->belongsTo(Customer::class,'customerID');
    }

    // admin
    public function admin()
    {
        return $this->belongsTo(Admin::class,'adminID');
    }

    // payment
    public function payment()
    {
        return $this->belongsTo(payment_method::class,'paymentID');
    }

    public function details()
    {
        return $this->hasMany(FoodInvoiceDetail::class, 'foodInvoiceID', 'foodInvoiceID');
    }
}