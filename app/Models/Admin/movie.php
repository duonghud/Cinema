<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class movie extends Model
{
    protected $table = 'movies';
    protected $primaryKey = 'movieID';
    public $timestamps = false;

    protected $fillable = [
        'movieTitle',
        'description',
        'director',
        'poster',
        'trailer',
        'releaseDate',
        'ageRatingID',
        'studioID',
    ];

    protected $casts = [
        'releaseDate' => 'date',
    ];


    public function genres()
    {
        return $this->belongsToMany(
            Genre::class,
            'genre_details',
            'movieID',
            'genreID'
        );
    }

    public function showTimes()
    {
        return $this->hasMany(ShowTime::class, 'movieID');
    }


    public function ageRating()
    {
        return $this->belongsTo(AgeRating::class, 'ageRatingID');
    }

    public function studio()
    {
        return $this->belongsTo(Studio::class, 'studioID');
    }

    
}
