@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-6 text-white">

    <h1 class="text-4xl font-bold text-center mb-2">
        Giá vé
    </h1>

    <p class="text-center text-sm opacity-70 mb-10">
        (Áp dụng từ ngày 01/06/2023)
    </p>

    <div class="space-y-12">

        <!-- Bảng giá -->
        <img src="http://apiv2.chieuphimquocgia.com.vn/Content/Images/Master/0019105.png"
            class="w-full rounded-xl shadow-lg">

        <!-- Phụ thu -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Phụ thu thời lượng phim</h2>
            <p class="text-gray-300 leading-8">
                Đối với phim có thời lượng từ <strong>150 phút trở lên</strong>:
                phụ thu <strong>10.000 VNĐ / vé</strong>.
            </p>

            <img src="http://apiv2.chieuphimquocgia.com.vn/Content/Images/Master/0019106.png"
                class="w-full rounded-xl shadow-lg mt-6">
        </section>

        <!-- Ưu đãi đối tượng -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Ưu đãi đối với đối tượng ưu tiên</h2>

            <ul class="list-disc pl-6 space-y-2 text-gray-300 leading-8">
                <li>Giảm 20%: Trẻ em dưới 16 tuổi, người cao tuổi từ 60 tuổi, người có công với cách mạng, hộ khó khăn.</li>
                <li>Giảm 50%: Người khuyết tật nặng.</li>
                <li>Giảm 100%: Người khuyết tật đặc biệt nặng, trẻ em dưới 0.7m đi cùng người lớn.</li>
            </ul>
        </section>

        <!-- Điều kiện -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Điều kiện áp dụng</h2>

            <ul class="list-disc pl-6 space-y-2 text-gray-300 leading-8">
                <li>Chỉ áp dụng khi mua vé tại quầy.</li>
                <li>Không áp dụng khi mua online.</li>
                <li>Phải xuất trình giấy tờ chứng minh khi mua vé và trước khi vào phòng chiếu.</li>
            </ul>
        </section>

        <!-- U22 -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Ưu đãi thành viên U22</h2>

            <p class="text-gray-300 leading-8">
                Đồng giá <strong>55.000đ/vé 2D</strong> từ Thứ 2 đến Thứ 6
                cho thành viên dưới 22 tuổi.
            </p>

            <ul class="list-disc pl-6 mt-3 space-y-2 text-gray-300 leading-8">
                <li>Chỉ áp dụng mua trực tiếp tại quầy.</li>
                <li>Không áp dụng ghế đôi.</li>
                <li>Mỗi thẻ mua tối đa 1 vé/ngày.</li>
                <li>Phải xuất trình thẻ U22 khi mua vé.</li>
            </ul>
        </section>

        <!-- Quy định độ tuổi -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Quy định độ tuổi xem phim</h2>

            <ul class="list-disc pl-6 space-y-2 text-gray-300 leading-8">
                <li>Khán giả phải xem phim đúng phân loại độ tuổi: P, K, T13, T16, T18, C.</li>
                <li>Không bán vé cho trẻ dưới 13 tuổi với suất chiếu kết thúc sau 22h00.</li>
                <li>Không bán vé cho trẻ dưới 16 tuổi với suất chiếu kết thúc sau 23h00.</li>
            </ul>
        </section>

        <!-- Giá lễ -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Ngày áp dụng giá vé Lễ / Tết</h2>

            <ul class="list-disc pl-6 space-y-2 text-gray-300 leading-8">
                <li>Tết Nguyên Đán, Tết Dương Lịch.</li>
                <li>Giỗ Tổ Hùng Vương, 30/4, 1/5, 2/9.</li>
                <li>14/2, 8/3, 24/12.</li>
                <li>Các ngày nghỉ bù lễ nếu trùng cuối tuần.</li>
            </ul>
        </section>

        <!-- Không áp dụng KM -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Lưu ý khuyến mại</h2>

            <p class="text-gray-300 leading-8">
                Không áp dụng đồng thời các chương trình ưu đãi/khuyến mại khác
                vào các ngày lễ, tết, suất chiếu sớm, suất chiếu đặc biệt.
            </p>
        </section>

        <!-- Liên hệ -->
        <section>
            <h2 class="text-2xl font-semibold mb-4">Liên hệ dịch vụ</h2>

            <div class="space-y-2 text-gray-300 leading-8">
                <p>Mua vé tập thể / hợp đồng khoán: <strong class="text-white">(024) 35148647</strong></p>
                <p>Thuê phòng / quảng cáo / dịch vụ khác: <strong class="text-white">(024) 35142856</strong></p>
            </div>
        </section>

    </div>

</div>
@endsection