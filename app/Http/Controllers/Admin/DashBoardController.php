<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\admin;
use App\Models\Admin\customer;
use App\Models\Admin\foodInvoice;
use App\Models\Admin\movie;
use App\Models\Admin\screeningRoom;
use App\Models\Admin\showTime;
use App\Models\Admin\ticket;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $today = Carbon::today();

        $stats = [
            'totalMovies' => movie::count(),
            'totalCustomers' => customer::count(),
            'totalRooms' => screeningRoom::count(),
            'totalAdmins' => admin::count(),
            'todayShowTimes' => showTime::whereDate('showDate', $today)->count(),
            'soldTickets' => ticket::count(),
            'todayTicketRevenue' => (float) ticket::whereHas('showTime', function ($query) use ($today) {
                $query->whereDate('showDate', $today);
            })->sum('price'),
            'todayFoodRevenue' => (float) foodInvoice::whereDate('orderDate', $today)->sum('total'),
        ];

        $upcomingShowTimes = showTime::with(['movie', 'room'])
            ->whereDate('showDate', '>=', $today)
            ->orderBy('showDate')
            ->orderBy('startTime')
            ->limit(6)
            ->get();

        $latestMovies = movie::with(['ageRating', 'studio'])
            ->orderByDesc('releaseDate')
            ->limit(5)
            ->get();

        return view('Admins.DashBoard.index', compact('stats', 'upcomingShowTimes', 'latestMovies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
