@extends('layouts.app')

@section('content')
<style>
    .schedule-shell {
        background:
            radial-gradient(circle at top left, rgba(255, 87, 87, 0.16), transparent 26%),
            radial-gradient(circle at top right, rgba(255, 184, 0, 0.12), transparent 24%),
            #10141B;
    }

    .schedule-date-tab {
        min-width: 104px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
        color: #cbd5e1;
        transition: all 0.25s ease;
    }

    .schedule-date-tab.is-active {
        background: linear-gradient(135deg, #ef4444, #f97316);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 14px 30px rgba(239, 68, 68, 0.28);
    }

    .schedule-card {
        background: linear-gradient(180deg, rgba(18, 24, 33, 0.96), rgba(11, 15, 22, 0.94));
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18);
    }

    .schedule-showtime {
        border: 1px solid rgba(248, 113, 113, 0.28);
        background: rgba(255, 255, 255, 0.03);
        transition: all 0.25s ease;
    }

    .schedule-showtime:hover {
        border-color: rgba(248, 113, 113, 0.95);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(248, 113, 113, 0.16);
    }
</style>

<div class="schedule-shell min-h-screen py-12 text-white">
    <div class="container mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if(session('error'))
            <div class="mb-6 rounded-2xl border border-red-500/40 bg-red-500/10 px-5 py-4 text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-5 py-4 text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="mb-2 text-sm uppercase tracking-[0.32em] text-red-300">Lịch chiếu</p>
                <h1 class="text-3xl font-black uppercase tracking-wide sm:text-4xl">Chọn phim và suất chiếu</h1>
                <p class="mt-3 max-w-2xl text-sm text-slate-300 sm:text-base">
                    Lọc theo ngày để xem nhanh phim đang chiếu và đặt vé ngay từ danh sách suất chiếu.
                </p>
            </div>
        </div>

        @if($scheduleDates->isEmpty())
            <div class="rounded-3xl border border-white/10 bg-white/5 px-6 py-16 text-center text-slate-300">
                Hiện tại chưa có lịch chiếu sắp tới.
            </div>
        @else
            <div class="mb-8 flex gap-3 overflow-x-auto pb-2" id="schedule-date-tabs">
                @foreach($scheduleDates as $index => $date)
                    @php($carbonDate = \Carbon\Carbon::parse($date))
                    <button
                        type="button"
                        class="schedule-date-tab {{ $index === 0 ? 'is-active' : '' }} flex shrink-0 flex-col rounded-3xl px-4 py-3 text-center"
                        data-date="{{ $date }}">
                        <span class="text-xs uppercase tracking-[0.2em]">{{ $carbonDate->translatedFormat('D') }}</span>
                        <span class="mt-1 text-2xl font-bold leading-none">{{ $carbonDate->format('d') }}</span>
                        <span class="mt-1 text-xs">{{ $carbonDate->format('m/Y') }}</span>
                    </button>
                @endforeach
            </div>

            @foreach($scheduleDates as $index => $date)
                @php($movies = $scheduleByDate->get($date, collect()))
                <div class="schedule-panel {{ $index === 0 ? '' : 'hidden' }}" data-date-panel="{{ $date }}">
                    @if($movies->isEmpty())
                        <div class="rounded-3xl border border-white/10 bg-white/5 px-6 py-16 text-center text-slate-300">
                            Ngày này chưa có phim đang mở bán.
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($movies as $item)
                                @php($movie = $item['movie'])
                                <article class="schedule-card rounded-[28px] p-4 sm:p-6">
                                    <div class="flex flex-col gap-5 lg:flex-row">
                                        <a href="{{ route('movies.show', $movie) }}" class="block w-full max-w-[210px] shrink-0 overflow-hidden rounded-3xl">
                                            <img
                                                src="{{ asset('posters/' . $movie->poster) }}"
                                                alt="{{ $movie->movieTitle }}"
                                                class="h-[300px] w-full object-cover">
                                        </a>

                                        <div class="flex-1">
                                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                                <div>
                                                    <div class="mb-2 flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.18em] text-slate-400">
                                                        <span>{{ $movie->ageRating->code ?? 'P' }}</span>
                                                        <span class="h-1 w-1 rounded-full bg-slate-600"></span>
                                                        <span>{{ $movie->genres->pluck('name')->join(', ') }}</span>
                                                    </div>

                                                    <h2 class="text-2xl font-extrabold uppercase tracking-wide text-white">
                                                        {{ $movie->movieTitle }}
                                                    </h2>

                                                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-slate-300">
                                                        <span>{{ $movie->duration ? $movie->duration . ' phút' : 'Chưa cập nhật thời lượng' }}</span>
                                                        <span>Khởi chiếu: {{ optional($movie->releaseDate)->format('d/m/Y') }}</span>
                                                    </div>
                                                </div>

                                                <a
                                                    href="{{ route('movies.show', ['movie' => $movie, 'showtime' => optional($item['showTimes']->first())->showTimeID]) }}"
                                                    class="inline-flex items-center rounded-full border border-red-400/35 px-4 py-2 text-sm font-semibold text-red-200 transition hover:border-red-300 hover:bg-red-500/10 hover:text-white">
                                                    Chi tiết phim
                                                </a>
                                            </div>

                                            <div class="mt-5 border-t border-white/10 pt-5">
                                                <p class="mb-4 text-sm font-semibold uppercase tracking-[0.24em] text-slate-400">
                                                    Suất chiếu trong ngày
                                                </p>

                                                <div class="flex flex-wrap gap-3">
                                                    @foreach($item['showTimes'] as $showTime)
                                                        <a
                                                            href="{{ route('movies.show', ['movie' => $movie, 'showtime' => $showTime->showTimeID]) }}"
                                                            class="schedule-showtime inline-flex min-w-[118px] flex-col rounded-2xl px-4 py-3 text-left text-slate-200">
                                                            <span class="text-lg font-bold leading-none">
                                                                {{ substr($showTime->startTime, 0, 5) }}
                                                            </span>
                                                            <span class="mt-2 text-xs uppercase tracking-[0.16em] text-slate-400">
                                                                {{ $showTime->room->roomName ?? 'Phòng chiếu' }}
                                                            </span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
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
