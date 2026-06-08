<?php

namespace App\Http\Controllers;

use App\Models\Admin\ShowTime;
use Carbon\Carbon;

class showController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        $upcomingShowTimes = ShowTime::with(['movie.genres', 'movie.ageRating', 'room'])
            ->whereDate('showDate', '>=', $today)
            ->orderBy('showDate')
            ->orderBy('startTime')
            ->get();

        $scheduleDates = $upcomingShowTimes
            ->pluck('showDate')
            ->map(fn($date) => Carbon::parse($date)->toDateString())
            ->unique()
            ->take(7)
            ->values();

        $allowedDates = $scheduleDates->flip();

        $scheduleByDate = $upcomingShowTimes
            ->filter(fn($showTime) => $allowedDates->has(Carbon::parse($showTime->showDate)->toDateString()))
            ->groupBy(fn($showTime) => Carbon::parse($showTime->showDate)->toDateString())
            ->map(function ($showTimes) {
                return $showTimes
                    ->groupBy('movieID')
                    ->map(function ($movieShowTimes) {
                        $movie = optional($movieShowTimes->first())->movie;

                        return $movie ? [
                            'movie' => $movie,
                            'showTimes' => $movieShowTimes,
                        ] : null;
                    })
                    ->filter()
                    ->values();
            });

        return view('system.schedule', compact('scheduleDates', 'scheduleByDate'));
    }
}
