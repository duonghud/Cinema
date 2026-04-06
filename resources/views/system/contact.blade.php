@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-10 px-4">
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden grid md:grid-cols-2">

        <!-- LEFT: Info -->
        <div class="bg-pink-500 text-white p-8 flex flex-col justify-center">
            <h2 class="text-3xl font-bold mb-4">Liên hệ với chúng tôi</h2>
            <p class="mb-6">Nếu bạn có câu hỏi hoặc cần hỗ trợ, hãy gửi tin nhắn cho chúng tôi.</p>

            <div class="space-y-3">
                <p> Địa chỉ: Hà Nội</p>
                <p> SĐT: 0123 456 789</p>
                <p> Email: support@flowershop.com</p>
            </div>
        </div>

        <!-- RIGHT: Form -->
        <div class="p-8">
            <h3 class="text-2xl font-semibold mb-6 text-gray-700">Gửi tin nhắn</h3>

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('contact.send') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-600 mb-1">Họ tên</label>
                    <input type="text" name="name"
                        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 mb-1">Email</label>
                    <input type="email" name="email"
                        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-600 mb-1">Tin nhắn</label>
                    <textarea name="message" rows="4"
                        class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400"
                        required></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-pink-500 text-white py-2 rounded-lg hover:bg-pink-600 transition">
                    Gửi
                </button>
            </form>
        </div>
    </div>
</div>
@endsection