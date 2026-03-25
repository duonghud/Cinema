<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class ticket extends Model
{
    protected $primaryKey = 'ticketID';
    public $timestamps = false;

    protected $fillable = [
        'price',
        'status',
        'showTimeID',
        'seatID'
    ];
    public function showTime()
    {
        return $this->belongsTo(ShowTime::class, 'showTimeID', 'showTimeID');
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seatID', 'seatID');
    }
}
