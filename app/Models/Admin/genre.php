<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class genre extends Model
{
    protected $table = 'genres';
    protected $primaryKey = 'genreID';
    public $timestamps = false;

    public function movies() {
        return $this->belongsToMany(
            Movie::class,
            'genre_details',
            'genreID',
            'movieID'
        );
    }
}
