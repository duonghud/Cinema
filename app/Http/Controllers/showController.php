<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Movie;
use App\Models\Admin\showTime;

class showController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        $showTimes = showTime::all();
        return view('Customer.showTime', compact('movies', 'showTimes'));
    }
}
