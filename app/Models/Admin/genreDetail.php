<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class genreDetail extends Model
{
    protected $table = 'genre_details';

    protected $primaryKey = null;
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'movieID',
        'genreID'
    ];
    public function movie()
    {
        return $this->belongsTo(Movie::class, 'movieID');
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class, 'genreID');
    }
}
