<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
    protected $primaryKey = 'ticketID';
    public $timestamps = false;

    protected $fillable = [
        'price',
        'status',
        'showTimeID',
        'seatID',
        'invoiceID',
    ];

    public function showTime()
    {
        return $this->belongsTo(ShowTime::class, 'showTimeID', 'showTimeID');
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seatID', 'seatID');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceID', 'invoiceID');
    }
}