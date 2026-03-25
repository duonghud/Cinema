<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\screenType;
use App\Models\Admin\Seat;
use App\Models\Admin\ShowTime;

class screeningRoom extends Model
{
    protected $table = 'screening_rooms';

    protected $primaryKey = 'roomID';

    public $timestamps = false;

    // cho phép insert/update bằng create() và update()
    protected $fillable = [
        'roomName',
        'capacity',
        'screenTypeID'
    ];

    // quan hệ với screen type
    public function screenType()
    {
        return $this->belongsTo(screenType::class, 'screenTypeID');
    }

    // quan hệ với seat
    public function seats()
    {
        return $this->hasMany(Seat::class, 'roomID');
    }

    // quan hệ với showtime
    public function showTimes()
    {
        return $this->hasMany(ShowTime::class, 'roomID');
    }
}