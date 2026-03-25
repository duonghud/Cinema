<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class showTime extends Model
{
    protected $table = 'show_times';
    protected $primaryKey = 'showTimeID';
    public $timestamps = false;

    protected $fillable = [
        'showDate',
        'startTime',
        'endTime',
        'movieID',
        'roomID',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movieID');
    }

    public function room()
    {
        return $this->belongsTo(ScreeningRoom::class, 'roomID');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'showTimeID');
    }
}
