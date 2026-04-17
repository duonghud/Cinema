<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Movie;
use App\Models\Admin\showTime;

class HomeController extends Controller
{
    public function index()
    {
        $movies = Movie::all();
        $banners = $movies->take(5);
        return view('system.welcome', compact('movies', 'banners'));
    }
}