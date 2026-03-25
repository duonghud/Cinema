<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class studio extends Model
{
    protected $table = 'studios';
    protected $primaryKey = 'studioID';
    public $timestamps = false;

    public function movies()
    {
        return $this->hasMany(Movie::class, 'studioID');
    }
}
