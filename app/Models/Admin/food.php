<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class food extends Model
{
    protected $table = 'food';
    protected $primaryKey = 'foodID';
    public $timestamps = false;


    protected $fillable = ['foodName', 'price', 'foodType', 'size'];
    public function foodInvoices()
    {
        return $this->belongsToMany(
            FoodInvoice::class,
            'foodInvoiceDetail',
            'foodID',
            'foodInvoiceID'
        )->withPivot('quantity');
    }
}
