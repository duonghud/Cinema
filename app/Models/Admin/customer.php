<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class customer extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'customerID';
    public $timestamps = false;

    protected $fillable = [
        'fullName','email','phoneNumber','address','password'
    ];

    public function invoices() {
        return $this->hasMany(Invoice::class, 'customerID');
    }

    public function foodInvoices() {
        return $this->hasMany(FoodInvoice::class, 'customerID');
    }
}
