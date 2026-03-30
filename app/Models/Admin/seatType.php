<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class seatType extends Model
{
    protected $table = 'seat_types';
    protected $primaryKey = 'seatTypeID';
    public $timestamps = false;

    protected $fillable = [
        'seatTypeName',
    ];

    public function seats() {
        return $this->hasMany(Seat::class, 'seatTypeID');
    }
}
