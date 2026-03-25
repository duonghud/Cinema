<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Movie;

class HomeController extends Controller
{
    public function index()
    {
        $movies = Movie::all(); // lấy tất cả phim
        return view('welcome', compact('movies'));
    }
}