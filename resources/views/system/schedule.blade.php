@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap');

    .schedule-shell {
        background: #0d1117;
        min-height: 100vh;
        font-family: 'Be Vietnam Pro', sans-serif;
    }

    .schedule-heading {
        text-align: center;
        padding: 36px 0 24px;
    }

    .schedule-heading .label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.02em;
    }

    .schedule-heading .label::before {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #ef4444;
        box-shadow: 0 0 8px rgba(239,68,68,0.7);
    }

    /* Date Tabs */
    .date-tabs {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 28px;
    }

    .date-tab {
        padding: 8px 20px;
        border-radius: 8px;
        border: 1.5px solid rgba(255,255,255,0.12);
        background: transparent;
        color: #94a3b8;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Be Vietnam Pro', sans-serif;
    }

    .date-tab:hover {
        border-color: rgba(239,68,68,0.5);
        color: #fff;
    }

    .date-tab.is-active {
        background: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }

    /* Grid Layout */
    .movies-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    @media (max-width: 768px) {
        .movies-grid { grid-template-columns: 1fr; }
    }

    /* Movie Card */
    .movie-poster {
        width: 200px;
        min-width: 200px;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .movie-poster img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease, filter 0.4s ease;
    }

    .movie-poster:hover img {
        transform: scale(1.08);
        filter: brightness(1.15) saturate(1.1);
    }

    .movie-poster::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(239,68,68,0.18) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
        z-index: 1;
    }

    .movie-poster:hover::after {
        opacity: 1;
    }

    .movie-format-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(15,20,30,0.82);
        color: #e2e8f0;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 6px;
        border: 1px solid rgba(255,255,255,0.18);
        letter-spacing: 0.04em;
    }

    .movie-info {
        flex: 1;
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
        gap: 7px;
        min-width: 0;
    }

    .movie-meta {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #64748b;
    }

    .movie-meta .dot {
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #475569;
        flex-shrink: 0;
    }

    .movie-title {
        font-size: 17px;
        font-weight: 800;
        color: #fff;
        letter-spacing: 0.01em;
        line-height: 1.3;
        margin-top: 2px;
    }

    .movie-origin {
        font-size: 13px;
        color: #94a3b8;
    }

    .movie-release {
        font-size: 13px;
        color: #94a3b8;
    }

    .movie-age {
        font-size: 13px;
        color: #f87171;
        line-height: 1.5;
    }

    .showtimes-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-top: 6px;
    }

    .showtimes-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 4px;
    }

    .showtime-btn {
        display: inline-block;
        padding: 7px 16px;
        border-radius: 8px;
        border: 1.5px solid rgba(255,255,255,0.15);
        background: transparent;
        color: #e2e8f0;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.22s ease;
        font-family: 'Be Vietnam Pro', sans-serif;
        white-space: nowrap;
        position: relative;
        overflow: hidden;
    }

    .showtime-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(239,68,68,0.18), rgba(249,115,22,0.12));
        opacity: 0;
        transition: opacity 0.22s ease;
    }

    .showtime-btn:hover {
        border-color: rgba(239,68,68,0.75);
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(239,68,68,0.22);
    }

    .showtime-btn:hover::before {
        opacity: 1;
    }

    .showtime-btn:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(239,68,68,0.15);
    }

    .movie-card {
        background: #161b24;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.07);
        display: flex;
        flex-direction: row;
        min-height: 260px;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    }

    .movie-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 48px rgba(0,0,0,0.38);
        border-color: rgba(239,68,68,0.22);
    }

    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: #475569;
        font-size: 15px;
    }
</style>

<div class="schedule-shell">
    <div class="container mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-10">

        @if(session('error'))
            <div style="margin-bottom:16px; padding:12px 16px; border-radius:10px; border:1px solid rgba(239,68,68,0.3); background:rgba(239,68,68,0.08); color:#fca5a5; font-size:14px;">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div style="margin-bottom:16px; padding:12px 16px; border-radius:10px; border:1px solid rgba(16,185,129,0.3); background:rgba(16,185,129,0.08); color:#6ee7b7; font-size:14px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="schedule-heading">
            <span class="label">Phim đang chiếu</span>
        </div>

        @if($scheduleDates->isEmpty())
            <div class="empty-state">Hiện tại chưa có lịch chiếu sắp tới.</div>
        @else
            <div class="date-tabs" id="schedule-date-tabs">
                @foreach($scheduleDates as $index => $date)
                    @php($carbonDate = \Carbon\Carbon::parse($date))
                    <button
                        type="button"
                        class="date-tab {{ $index === 0 ? 'is-active' : '' }}"
                        data-date="{{ $date }}">
                        {{ $carbonDate->format('d-m-Y') }}
                    </button>
                @endforeach
            </div>

            @foreach($scheduleDates as $index => $date)
                @php($movies = $scheduleByDate->get($date, collect()))
                <div class="schedule-panel {{ $index === 0 ? '' : 'hidden' }}" data-date-panel="{{ $date }}">
                    @if($movies->isEmpty())
                        <div class="empty-state">Ngày này chưa có phim đang mở bán.</div>
                    @else
                        <div class="movies-grid">
                            @foreach($movies as $item)
                                @php($movie = $item['movie'])
                                <div class="movie-card">
                                    <div class="movie-poster">
                                        <a href="{{ route('movies.show', $movie) }}">
                                            <img
                                                src="{{ asset('posters/' . $movie->poster) }}"
                                                alt="{{ $movie->movieTitle }}">
                                        </a>
                                        <span class="movie-format-badge">2D</span>
                                    </div>

                                    <div class="movie-info">
                                        <div class="movie-meta">
                                            <span>{{ $movie->genres->pluck('name')->first() ?? 'Phim' }}</span>
                                            @if($movie->duration)
                                                <span class="dot"></span>
                                                <span>{{ $movie->duration }} phút</span>
                                            @endif
                                        </div>

                                        <a href="{{ route('movies.show', $movie) }}" style="text-decoration:none;">
                                            <div class="movie-title">{{ $movie->movieTitle }}</div>
                                        </a>

                                        <div class="movie-origin">Xuất xứ: {{ $movie->country ?? 'Việt Nam' }}</div>

                                        <div class="movie-release">Khởi chiếu: {{ optional($movie->releaseDate)->format('d/m/Y') }}</div>

                                        @if($movie->ageRating)
                                            <div class="movie-age">
                                                {{ $movie->ageRating->description ?? 'Phim được phổ biến đến người xem từ đủ tuổi trở lên (' . ($movie->ageRating->code ?? 'P') . ')' }}
                                            </div>
                                        @endif

                                        <div class="showtimes-label">Lịch chiếu</div>

                                        <div class="showtimes-list">
                                            @foreach($item['showTimes'] as $showTime)
                                                <a
                                                    href="{{ route('movies.show', ['movie' => $movie, 'showtime' => $showTime->showTimeID]) }}"
                                                    class="showtime-btn">
                                                    {{ substr($showTime->startTime, 0, 5) }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('[data-date]');
        const panels = document.querySelectorAll('[data-date-panel]');

        tabs.forEach((tab) => {
            tab.addEventListener('click', function () {
                const targetDate = this.dataset.date;
                tabs.forEach((item) => item.classList.remove('is-active'));
                this.classList.add('is-active');
                panels.forEach((panel) => {
                    panel.classList.toggle('hidden', panel.dataset.datePanel !== targetDate);
                });
            });
        });
    });
</script>
@endsection