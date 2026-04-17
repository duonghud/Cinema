@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#020817] text-white px-6 py-10">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- LEFT SIDE --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-[#111827] rounded-2xl p-8 shadow-lg border border-slate-800">
                <h2 class="text-2xl font-bold mb-6">Thông tin phim</h2>

                <div class="grid grid-cols-2 gap-8 text-lg">
                    <div>
                        <p class="text-slate-400">Phim</p>
                        <p class="font-bold">{{ $booking['movie'] }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Ghế</p>
                        <p class="font-bold">{{ implode(', ', $booking['seats']) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Ngày giờ chiếu</p>
                        <p class="text-orange-400 font-bold">{{ $booking['time'] }} - {{ $booking['date'] }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Phòng chiếu</p>
                        <p class="font-bold">{{ $booking['room'] }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400">Định dạng</p>
                        <p class="font-bold">{{ $booking['format'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#111827] rounded-2xl p-8 shadow-lg border border-slate-800">
                <h2 class="text-2xl font-bold mb-6">Thông tin thanh toán</h2>

                <table class="w-full border border-slate-700 rounded-xl overflow-hidden">
                    <thead class="bg-slate-900">
                        <tr>
                            <th class="p-4 text-left">Danh mục</th>
                            <th class="p-4 text-left">Số lượng</th>
                            <th class="p-4 text-left">Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-t border-slate-700">
                            <td class="p-4">Ghế ({{ implode(', ', $booking['seats']) }})</td>
                            <td class="p-4">{{ count($booking['seats']) }}</td>
                            <td class="p-4">{{ number_format($booking['total']) }}đ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RIGHT SIDE --}}
        <div class="bg-[#111827] rounded-2xl p-8 shadow-lg border border-slate-800 h-fit">
            <h2 class="text-2xl font-bold mb-6">Phương thức thanh toán</h2>

            <form action="{{ route('customer.payment.process') }}" method="POST">
                @csrf

                <div class="space-y-4 mb-8">
                    @foreach($paymentMethods as $method)
                        <label class="flex items-center gap-4 border border-slate-700 rounded-xl px-4 py-4 hover:border-red-500 cursor-pointer">
                            <input type="radio" name="payment_method" value="{{ $method->paymentID }}" class="accent-red-500" required>
                            <span class="font-semibold">{{ $method->name }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="space-y-2 text-lg mb-6">
                    <div class="flex justify-between">
                        <span>Thanh toán</span>
                        <span>{{ number_format($booking['total']) }}đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí</span>
                        <span>0đ</span>
                    </div>
                    <div class="flex justify-between font-bold text-xl">
                        <span>Tổng cộng</span>
                        <span>{{ number_format($booking['total']) }}đ</span>
                    </div>
                </div>

                <label class="flex items-start gap-3 mb-6 text-sm text-slate-300">
                    <input type="checkbox" required class="mt-1 accent-red-500">
                    <span>Tôi xác nhận các thông tin đã chính xác và đồng ý với điều khoản & chính sách</span>
                </label>

                <button class="w-full bg-red-600 hover:bg-red-700 py-4 rounded-xl font-bold text-lg transition">
                    Thanh toán
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
