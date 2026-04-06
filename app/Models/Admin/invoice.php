<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\customer;
use App\Models\Admin\payment_method;

class invoice extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoiceID';
    public $timestamps = false;

    protected $fillable = [
        'createDate',
        'totalAmount',
        'customerID',
        'adminID',
        'paymentID'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'adminID');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(payment_method::class, 'paymentID');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'invoiceID');
    }
}
