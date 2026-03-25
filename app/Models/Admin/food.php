<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class food extends Model
{
    protected $table = 'food';
    protected $primaryKey = 'foodID';
    public $timestamps = false;

    public function foodInvoices() {
        return $this->belongsToMany(
            FoodInvoice::class,
            'foodInvoiceDetail',
            'foodID',
            'foodInvoiceID'
        )->withPivot('quantity');
    }
}
