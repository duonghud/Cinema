<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $table = 'seats';

    protected $primaryKey = 'seatID';

    public $timestamps = false;

    protected $fillable = [
        'rowSeat',
        'colSeat',
        'roomID',
        'seatTypeID'
    ];

    // quan hệ phòng chiếu
    public function screeningRoom()
    {
        return $this->belongsTo(screeningRoom::class, 'roomID');
    }

    // quan hệ loại ghế
    public function seatType()
    {
        return $this->belongsTo(SeatType::class, 'seatTypeID');
    }
}

