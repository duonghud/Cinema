<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Movie;
use App\Models\Admin\showTime;

class HomeController extends Controller
{
    public function index()
    {
        // Phim có suất chiếu (đang chiếu)
        $nowShowing = Movie::whereHas('showTimes')->get();

        // Phim chưa có suất chiếu (sắp chiếu)
        $comingSoon = Movie::whereDoesntHave('showTimes')->get();

        // Banner = phim có suất chiếu (có thể limit)
        $banners = Movie::whereHas('showTimes')->take(5)->get();

        return view('system.welcome', compact('nowShowing', 'comingSoon', 'banners'));
    }
}
