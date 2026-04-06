<?php

namespace App\Models\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class customer extends Authenticatable
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
