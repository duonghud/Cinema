<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class foodInvoiceDetail extends Model
{
    protected $table = 'food_invoice_details';

    // composite primary key
    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'foodInvoiceID',
        'foodID',
        'quantity'
    ];
    public function foodInvoice()
    {
        return $this->belongsTo(FoodInvoice::class, 'foodInvoiceID');
    }

    public function food()
    {
        return $this->belongsTo(Food::class, 'foodID');
    }
}