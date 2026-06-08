@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-12 px-6">

    <div class="grid md:grid-cols-2 gap-10 items-start text-white">

        <!-- Nội dung -->
        <div>
            <h1 class="text-3xl font-bold mb-8 text-white">
                Giới thiệu về Trung tâm Chiếu phim Quốc gia
            </h1>

            <div class="space-y-5 text-white leading-8 text-[17px]">
                <p>
                    <strong>Trung tâm Chiếu phim Quốc gia</strong>
                    (tên giao dịch quốc tế là <strong>National Cinema Center</strong>)
                    là đơn vị sự nghiệp công lập trực thuộc
                    <strong>Bộ Văn hóa, Thể thao và Du lịch</strong>,
                    có chức năng tổ chức chiếu phim phục vụ các nhiệm vụ chính trị, xã hội,
                    hợp tác quốc tế; trưng bày điện ảnh; điều tra xã hội học về nhu cầu khán giả
                    để phục vụ cho công tác định hướng phát triển ngành điện ảnh.
                </p>

                <p><strong>Ngày thành lập:</strong> 29/12/2026  </p>
                <p><strong>Trụ sở:</strong> 87 Láng Hạ, Phường Ô Chợ Dừa, Thành phố Hà Nội</p>

                <p>
                    <strong>Website:</strong>
                    <a href="{{ route('home') }}"
                        target="_blank"
                        class="text-blue-300 hover:underline">
                        www.vaicinema.com.vn
                    </a>
                </p>

                <p><strong>Email:</strong> pdichvuncc@gmail.com</p>
                <p><strong>Số điện thoại:</strong> 024.3514 1791 / 024.3514 8647</p>
            </div>
        </div>

        <!-- Bản đồ -->
        <div>
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.3964834468625!2d105.81563109999999!3d21.016816!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab649ee1bf5d%3A0x559afaae4a7e55fc!2zODcgUC4gTMOhbmcgSOG6oSwgQ2jhu6MgROG7q2EsIMOUIENo4bujIEThu6thLCBIw6AgTuG7mWksIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1776012401048!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>

    </div>

</div>
@endsection