<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use App\Models\Admin\screeningRoom;
use App\Models\Admin\screenType;
use App\Models\Admin\Seat;
use App\Models\Admin\seatType;
use App\Models\Admin\showTime;

class screeningRoomController extends Controller
{
    /**
     * Hiển thị danh sách phòng chiếu có kèm bộ lọc tìm kiếm và phân trang.
     */
    public function index(Request $request)
    {
        // Lấy từ khóa tìm kiếm và loại màn hình từ request
        $search     = trim((string) $request->input('search'));
        $screenType = $request->input('screenType');

        // Khởi tạo query lấy phòng chiếu kèm theo mối quan hệ loại màn hình (Eager Loading)
        $room = screeningRoom::with('screenType')
            // Bộ lọc tìm kiếm theo từ khóa (Nếu có $search)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('roomID',   'like', "%{$search}%")
                      ->orWhere('roomName', 'like', "%{$search}%")
                      ->orWhere('capacity', 'like', "%{$search}%")
                      // Tìm kiếm theo tên của loại màn hình thuộc phòng đó
                      ->orWhereHas('screenType', function ($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            })
            // Bộ lọc chính xác theo loại màn hình (Nếu có $screenType)
            ->when($screenType, function ($query) use ($screenType) {
                $query->where('screenTypeID', $screenType);
            })
            ->paginate(20)          // Phân trang: 5 bản ghi trên 1 trang
            ->withQueryString();   // Giữ lại các tham số trên URL khi chuyển trang

        // Lấy tất cả các loại màn hình để hiển thị lên thẻ <select> ở bộ lọc ngoài View
        $screenTypes = screenType::all();

        return view(
            'admins.manageCinema.screeningRoom.index',
            compact('room', 'screenTypes')
        );
    }

    /**
     * Hiển thị form tạo mới phòng chiếu.
     */
    public function create()
    {
        // Lấy danh sách để đổ dữ liệu vào các ô Select trong form
        $screenTypes = screenType::all();
        $seatTypes   = seatType::all();

        // Tự động tìm ID mặc định cho từng nhóm ghế dựa vào tên (VIP, Thường, Đôi) bằng Helper tuần tự bên dưới
        $defaultVipTypeID    = $seatTypes->first(fn($s) => $this->isVip($s->seatTypeName))?->seatTypeID;
        $defaultNormalTypeID = $seatTypes->first(fn($s) => $this->isNormal($s->seatTypeName))?->seatTypeID;
        $defaultDoubleTypeID = $seatTypes->first(fn($s) => $this->isDouble($s->seatTypeName))?->seatTypeID;

        return view(
            'admins.manageCinema.screeningRoom.create',
            compact('screenTypes', 'seatTypes', 'defaultVipTypeID', 'defaultNormalTypeID', 'defaultDoubleTypeID')
        );
    }

    /**
     * Lưu thông tin phòng chiếu mới và tự động rải ghế vào Database.
     */
    public function store(Request $request)
    {
        // 1. Kiểm tra dữ liệu đầu vào (Validation)
        $validated = $request->validate([
            'roomName'         => 'required|string|max:100|unique:screening_rooms,roomName',
            'rows'             => 'required|integer|min:1|max:26', // Giới hạn từ A -> Z
            'cols'             => 'required|integer|min:1|max:50', // Tối đa 50 cột

            'vipSeats'         => 'required|integer|min:0',
            'normalSeats'      => 'required|integer|min:0',
            'doubleSeats'      => ['required', 'integer', 'min:0', function ($attr, $value, $fail) {
                // Custom rule: Ghế đôi bắt buộc phải nhập số chẵn (cặp 2 chỗ)
                if ((int) $value > 0 && (int) $value % 2 !== 0) {
                    $fail('Số ghế đôi phải là số chẵn (ghế đôi luôn đi theo cặp 2 chỗ).');
                }
            }],

            'vipSeatTypeID'    => 'required|exists:seat_types,seatTypeID',
            'normalSeatTypeID' => 'required|exists:seat_types,seatTypeID',
            'doubleSeatTypeID' => 'required|exists:seat_types,seatTypeID',

            'screenTypeID'     => 'required|exists:screen_types,screenTypeID',
        ], [
            // Custom thông báo lỗi bằng tiếng Việt
            'roomName.unique' => 'Tên phòng đã tồn tại',
            'cols.max'        => 'Số cột tối đa là 50',
            'rows.max'        => 'Số hàng tối đa là 26 (A-Z)',
        ]);

        // 2. Tính toán tổng số lượng ghế và kiểm tra tính logic của lưới sơ đồ
        $vipCount    = (int) $validated['vipSeats'];
        $normalCount = (int) $validated['normalSeats'];
        $doubleCount = (int) $validated['doubleSeats'];
        $totalSlots  = $vipCount + $normalCount + $doubleCount;
        $maxRows     = (int) $validated['rows'];
        $maxCols     = (int) $validated['cols'];

        if ($totalSlots <= 0) {
            return back()->withInput()->withErrors(['capacity' => 'Phòng phải có ít nhất 1 ghế']);
        }

        // Chặn trường hợp nhập tổng số ghế lớn hơn kích thước lưới (Ví dụ: lưới 5x5=25 ô nhưng đòi đặt 30 ghế)
        if ($totalSlots > $maxRows * $maxCols) {
            return back()->withInput()
                ->withErrors(['vipSeats' => 'Tổng số ghế vượt quá số ô trong lưới (' . $totalSlots . '/' . ($maxRows * $maxCols) . ')']);
        }

        // 3. Tạo phòng chiếu mới
        $room = ScreeningRoom::create([
            'roomName'     => $validated['roomName'],
            'capacity'     => $totalSlots,
            'screenTypeID' => $validated['screenTypeID'],
        ]);

        // Lưu cấu hình lưới (Hàng x Cột) vào Cache vĩnh viễn để phục vụ việc hiển thị form edit/sơ đồ
        Cache::forever("room_grid_{$room->roomID}", [
            'rows' => $maxRows,
            'cols' => $maxCols,
        ]);

        // 4. Tạo hàng đợi (Queue) danh sách các ghế cần rải vào phòng (Xếp thứ tự ưu tiên: VIP -> Thường -> Đôi)
        $seatQueue = [];
        for ($i = 0; $i < $vipCount;    $i++) $seatQueue[] = ['typeID' => (int) $validated['vipSeatTypeID']];
        for ($i = 0; $i < $normalCount; $i++) $seatQueue[] = ['typeID' => (int) $validated['normalSeatTypeID']];
        for ($i = 0; $i < $doubleCount; $i++) $seatQueue[] = ['typeID' => (int) $validated['doubleSeatTypeID']];

        // 5. Thuật toán rải ghế tự động vào ma trận dựa trên Số hàng và Số cột
        $rows       = range('A', 'Z'); // Mảng chữ cái từ A-Z đại diện cho tên hàng
        $rowIndex   = 0;               // Bắt đầu từ hàng A (chỉ mục mảng là 0)
        $currentCol = 1;               // Bắt đầu từ cột số 1
        $insertData = [];              // Mảng trung gian chuẩn bị dữ liệu để insert số lượng lớn (Bulk Insert)

        foreach ($seatQueue as $item) {
            // Nếu vượt quá giới hạn hàng của phòng hoặc vượt mảng chữ cái A-Z thì dừng lại
            if ($rowIndex >= $maxRows || $rowIndex >= count($rows)) break;

            $insertData[] = [
                'roomID'     => $room->roomID,
                'rowSeat'    => $rows[$rowIndex], // Lưu ký tự hàng (A, B, C...)
                'colSeat'    => $currentCol,      // Lưu số cột (1, 2, 3...)
                'seatTypeID' => $item['typeID'],
            ];

            $currentCol++;
            // Nếu cột hiện tại vượt quá số cột tối đa của hàng -> Tự động xuống hàng tiếp theo và reset cột về 1
            if ($currentCol > $maxCols) {
                $currentCol = 1;
                $rowIndex++;
            }
        }

        // 6. Thực hiện lưu toàn bộ ghế vào DB chỉ với 1 câu lệnh Insert (Tối ưu hiệu năng)
        if (!empty($insertData)) {
            Seat::insert($insertData);
        }

        // Chuyển hướng sang trang quản lý sơ đồ ghế chi tiết của phòng vừa tạo
        return redirect()
            ->route('seat.index', ['roomID' => $room->roomID])
            ->with('success', 'Tạo phòng và ghế thành công');
    }

    /**
     * Hiển thị form chỉnh sửa thông tin phòng chiếu và cấu hình ghế hiện tại.
     */
    public function edit(string $id)
    {
        // Tìm phòng chiếu cần sửa, nếu không thấy trả về lỗi 404
        $room        = screeningRoom::findOrFail($id);
        $seatTypes   = seatType::all();
        $screenTypes = screenType::all();

        // Lấy tất cả các ghế hiện đang có trong phòng chiếu này
        $seats = Seat::where('roomID', $room->roomID)->get();

        // Tính toán kích thước thực tế dựa trên dữ liệu ghế trong DB
        $actualCols = $seats->max(fn($s) => (int) $s->colSeat) ?: 1;
        $actualRows = $seats->pluck('rowSeat')->unique()->count() ?: 1;

        // Mảng mẫu dùng để đếm số lượng từng loại ghế đang có trong phòng
        $seatCounts = [
            'vipSeats'         => 0,
            'vipSeatTypeID'    => null,
            'normalSeats'      => 0,
            'normalSeatTypeID' => null,
            'doubleSeats'      => 0,
            'doubleSeatTypeID' => null,
        ];

        // Gom nhóm ghế theo ID loại ghế để bắt đầu đếm số lượng
        $grouped = $seats->groupBy('seatTypeID');

        foreach ($grouped as $typeID => $group) {
            $seatType = $seatTypes->firstWhere('seatTypeID', $typeID);
            if (!$seatType) continue;

            $name = mb_strtolower($seatType->seatTypeName, 'UTF-8');

            // Phân loại ghế dựa vào tên loại ghế để gán số lượng tương ứng
            if ($this->isDouble($name)) {
                $seatCounts['doubleSeats']      = $group->count();
                $seatCounts['doubleSeatTypeID'] = $typeID;
            } elseif ($this->isVip($name)) {
                $seatCounts['vipSeats']      = $group->count();
                $seatCounts['vipSeatTypeID'] = $typeID;
            } else {
                $seatCounts['normalSeats']      = $group->count();
                $seatCounts['normalSeatTypeID'] = $typeID;
            }
        }

        // Đọc cấu hình kích thước lưới (Grid) từ Cache đã lưu khi tạo phòng
        $grid        = Cache::get("room_grid_{$room->roomID}");
        // Lấy giá trị lớn nhất giữa thực tế trong DB và trong Cache để điền (pre-fill) vào form nhập liệu tránh lỗi lệch form
        $actualRows  = max($actualRows, (int) ($grid['rows'] ?? 0));
        $actualCols  = max($actualCols, (int) ($grid['cols'] ?? 0));

        // Thiết lập ID mặc định gửi ra form hiển thị (nếu phòng chưa có loại ghế đó thì tìm tự động dựa trên tên)
        $defaultVipTypeID    = $seatCounts['vipSeatTypeID']
            ?? $seatTypes->first(fn($s) => $this->isVip($s->seatTypeName))?->seatTypeID;
        $defaultNormalTypeID = $seatCounts['normalSeatTypeID']
            ?? $seatTypes->first(fn($s) => $this->isNormal($s->seatTypeName))?->seatTypeID;
        $defaultDoubleTypeID = $seatCounts['doubleSeatTypeID']
            ?? $seatTypes->first(fn($s) => $this->isDouble($s->seatTypeName))?->seatTypeID;

        return view(
            'admins.manageCinema.screeningRoom.edit',
            compact(
                'room', 'seatTypes', 'screenTypes',
                'seatCounts', 'actualRows', 'actualCols',
                'defaultVipTypeID', 'defaultNormalTypeID', 'defaultDoubleTypeID'
            )
        );
    }

    /**
     * Cập nhật thông tin phòng chiếu và tái cấu trúc lại toàn bộ sơ đồ ghế.
     */
    public function update(Request $request, $id)
    {
        $room = screeningRoom::findOrFail($id);

        // 1. Kiểm tra dữ liệu cập nhật từ form gửi lên
        $validated = $request->validate([
            'roomName'         => 'required|string|max:100',
            'rows'             => 'required|integer|min:1|max:26',
            'cols'             => 'required|integer|min:1|max:50',
            'screenTypeID'     => 'required|exists:screen_types,screenTypeID',
            'vipSeats'         => 'nullable|integer|min:0',
            'vipSeatTypeID'    => 'nullable|exists:seat_types,seatTypeID',
            'normalSeats'      => 'nullable|integer|min:0',
            'normalSeatTypeID' => 'nullable|exists:seat_types,seatTypeID',
            'doubleSeats'      => ['nullable', 'integer', 'min:0', function ($attr, $value, $fail) {
                if ((int) $value > 0 && (int) $value % 2 !== 0) {
                    $fail('Số ghế đôi phải là số chẵn (ghế đôi luôn đi theo cặp 2 chỗ).');
                }
            }],
            'doubleSeatTypeID' => 'nullable|exists:seat_types,seatTypeID',
        ], [
            // Thông báo lỗi tiếng việt tương ứng
            'roomName.required'     => 'Tên phòng không được để trống.',
            'roomName.max'          => 'Tên phòng không được quá 100 ký tự.',
            'screenTypeID.required' => 'Vui lòng chọn loại phòng.',
            'screenTypeID.exists'   => 'Loại phòng không hợp lệ.',
            'rows.max'              => 'Số hàng tối đa là 26 (A-Z)',
            'cols.max'              => 'Số cột tối đa là 50',
        ]);

        // 2. Ép kiểu dữ liệu và tính toán kiểm tra logic lưới ghế mới giống như khi tạo mới
        $vipCount    = (int) ($validated['vipSeats']    ?? 0);
        $normalCount = (int) ($validated['normalSeats'] ?? 0);
        $doubleCount = (int) ($validated['doubleSeats'] ?? 0);
        $totalSlots  = $vipCount + $normalCount + $doubleCount;
        $maxRows     = (int) $validated['rows'];
        $maxCols     = (int) $validated['cols'];

        if ($totalSlots > $maxRows * $maxCols) {
            return back()->withInput()
                ->withErrors(['vipSeats' => 'Tổng số ghế vượt quá số ô trong lưới (' . $totalSlots . '/' . ($maxRows * $maxCols) . ')']);
        }

        // 3. Cập nhật thông tin cơ bản của phòng chiếu
        $room->update([
            'roomName'     => $validated['roomName'],
            'screenTypeID' => $validated['screenTypeID'],
            'capacity'     => $totalSlots,
        ]);

        // Cập nhật/ghi đè lại cấu hình lưới mới vào Cache vĩnh viễn
        Cache::forever("room_grid_{$room->roomID}", [
            'rows' => $maxRows,
            'cols' => $maxCols,
        ]);

        // 4. Nếu có ghế, tiến hành xóa sạch ghế cũ trong phòng đi và thực hiện rải lại ghế mới từ đầu
        if ($totalSlots > 0) {
            // Xóa toàn bộ ghế cũ liên quan đến phòng chiếu này
            Seat::where('roomID', $room->roomID)->delete();

            // Chuẩn bị lại hàng đợi danh sách loại ghế mới
            $seatQueue = [];
            for ($i = 0; $i < $vipCount;    $i++) $seatQueue[] = (int) ($validated['vipSeatTypeID']    ?? 0);
            for ($i = 0; $i < $normalCount; $i++) $seatQueue[] = (int) ($validated['normalSeatTypeID'] ?? 0);
            for ($i = 0; $i < $doubleCount; $i++) $seatQueue[] = (int) ($validated['doubleSeatTypeID'] ?? 0);

            // Chạy lại thuật toán ma trận rải ghế tự động (Hàng & Cột)
            $rows       = range('A', 'Z');
            $rowIndex   = 0;
            $currentCol = 1;
            $insertData = [];

            foreach ($seatQueue as $typeID) {
                if ($rowIndex >= $maxRows || $rowIndex >= count($rows)) break;

                $insertData[] = [
                    'roomID'     => $room->roomID,
                    'rowSeat'    => $rows[$rowIndex],
                    'colSeat'    => $currentCol,
                    'seatTypeID' => $typeID,
                ];

                $currentCol++;
                if ($currentCol > $maxCols) {
                    $currentCol = 1;
                    $rowIndex++;
                }
            }

            // Tiến hành lưu hàng loạt ghế mới vào Database
            if (!empty($insertData)) {
                Seat::insert($insertData);
            }
        }

        return redirect()
            ->route('seat.index', ['roomID' => $room->roomID])
            ->with('success', 'Cập nhật phòng chiếu thành công.');
    }

    /**
     * Xóa phòng chiếu kèm theo các dữ liệu ghế và cache liên quan.
     */
    public function destroy(string $id)
    {
        $room = screeningRoom::findOrFail($id);

        // Kiểm tra ràng buộc: Nếu phòng chiếu này đã được lên Lịch chiếu (Showtime) thì CHẶN KHÔNG CHO XÓA
        if (showTime::where('roomID', $room->roomID)->exists()) {
            return redirect()
                ->route('screeningRoom.index')
                ->with('error', 'Phòng đang có suất chiếu, không thể xóa.');
        }

        // 1. Xóa toàn bộ ghế thuộc phòng này trước (Tránh lỗi ràng buộc khóa ngoại nếu có)
        Seat::where('roomID', $room->roomID)->delete();

        // 2. Xóa cấu hình thông số lưới (Hàng x Cột) lưu ở Cache của phòng này
        Cache::forget("room_grid_{$room->roomID}");

        // 3. Xóa chính thức bản ghi phòng chiếu trong Database
        $room->delete();

        return redirect()
            ->route('screeningRoom.index')
            ->with('success', 'Xóa phòng thành công.');
    }

    /*
    |--------------------------------------------------------------------------
    | Các hàm Helper: phân loại tên ghế (Dùng nội bộ trong Controller này)
    |--------------------------------------------------------------------------
    */
    
    /**
     * Kiểm tra xem tên ghế có phải là loại ghế Đôi (Double/Couple) hay không.
     */
    private function isDouble(string $name): bool
    {
        $name = mb_strtolower($name, 'UTF-8'); // Chuyển chuỗi về chữ thường để so sánh không phân biệt hoa thường
        return str_contains($name, 'đôi')
            || str_contains($name, 'double')
            || str_contains($name, 'couple');
    }

    /**
     * Kiểm tra xem tên ghế có phải là loại ghế VIP hay không.
     */
    private function isVip(string $name): bool
    {
        $name = mb_strtolower($name, 'UTF-8');
        return str_contains($name, 'vip');
    }

    /**
     * Nếu không phải ghế Đôi và cũng không phải VIP thì mặc định tính là ghế Thường (Normal).
     */
    private function isNormal(string $name): bool
    {
        return !$this->isDouble($name) && !$this->isVip($name);
    }
}