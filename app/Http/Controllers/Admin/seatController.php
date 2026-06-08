<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Admin\Seat;
use App\Models\Admin\ScreeningRoom;
use App\Models\Admin\SeatType;
use App\Models\Admin\Ticket;
use App\Models\Admin\ShowTime;

class SeatController extends Controller
{
    /**
     * Hàm Helper nội bộ: Tìm kiếm ID loại ghế trong Database dựa trên danh sách từ khóa tương đồng.
     */
    private function findSeatTypeIdByKeywords(array $keywords): ?int
    {
        $query = SeatType::query();
        foreach ($keywords as $index => $keyword) {
            // Vòng lặp đầu tiên dùng 'where', các vòng lặp sau nối thêm bằng 'orWhere'
            $method = $index === 0 ? 'where' : 'orWhere';
            $query->{$method}('seatTypeName', 'like', '%' . $keyword . '%');
        }
        return $query->value('seatTypeID'); // Trả về giá trị ID đầu tiên tìm thấy hoặc null
    }

    /**
     * Lấy ID của loại ghế đôi (Couple / Double). Nếu không thấy, mặc định trả về ID là 3.
     */
    private function getCoupleTypeId(): int
    {
        return $this->findSeatTypeIdByKeywords(['đôi', 'Đôi', 'couple', 'double']) ?? 3;
    }

    /**
     * Lấy ID của loại ghế bảo trì (Maintenance). Nếu không thấy, mặc định trả về ID là 4.
     */
    private function getMaintenanceTypeId(): int
    {
        return $this->findSeatTypeIdByKeywords(['bảo trì', 'Bảo trì', 'maintenance']) ?? 4;
    }

    /**
     * Lấy ID của loại ghế thường (Normal). Nếu không thấy, mặc định trả về ID là 2.
     */
    private function getNormalTypeId(): int
    {
        return $this->findSeatTypeIdByKeywords(['thường', 'Thường', 'normal']) ?? 2;
    }

    // ================= HIỂN THỊ DANH SÁCH GHẾ =================
    /**
     * Giao diện quản lý/sơ đồ ghế của từng phòng chiếu (Hỗ trợ tìm kiếm, phân trang).
     */
    public function index(Request $request)
    {
        // 1. Lấy danh sách tất cả các phòng chiếu phục vụ thanh Select chọn phòng
        $rooms  = ScreeningRoom::all();
        // Lấy roomID từ request, nếu không truyền lên thì mặc định lấy phòng đầu tiên trong danh sách
        $roomID = (int) ($request->input('roomID') ?? optional($rooms->first())->roomID);
        $room   = ScreeningRoom::find($roomID);

        // Trường hợp ID phòng truyền lên không hợp lệ nhưng danh sách phòng không rỗng -> Fallback về phòng đầu tiên
        if (!$room && $rooms->isNotEmpty()) {
            $room   = $rooms->first();
            $roomID = (int) $room->roomID;
        }

        $seatTypes = SeatType::all();

        // Chuẩn bị mảng ID đặc biệt phục vụ việc phân loại màu sắc/giao diện ghế ngoài View
        $specialTypeIds = [
            'vip'         => $this->findSeatTypeIdByKeywords(['vip']) ?? 1,
            'normal'      => $this->getNormalTypeId(),
            'couple'      => $this->getCoupleTypeId(),
            'maintenance' => $this->getMaintenanceTypeId(),
        ];
        // Chuyển danh sách loại ghế thành mảng [ID => Tên] để tra cứu nhanh
        $seatTypeNames = $seatTypes->pluck('seatTypeName', 'seatTypeID')->toArray();

        // Nếu hệ thống hoàn toàn chưa có phòng chiếu nào, trả về View trống với các thông số mặc định
        if (!$room) {
            return view('admins.manageCinema.seat.index', [
                'seats'          => collect(),
                'room'           => null,
                'rooms'          => $rooms,
                'seatTypes'      => $seatTypes,
                'roomID'         => 0,
                'specialTypeIds' => $specialTypeIds,
                'seatTypeNames'  => $seatTypeNames,
                'roomActualRows' => 1,
                'roomActualCols' => 1,
            ]);
        }

        // 2. Xử lý tìm kiếm từ khóa trong phòng chiếu đang chọn
        $search = trim((string) $request->input('search'));

        $seats = Seat::with(['seatType', 'screeningRoom'])
            ->where('roomID', $roomID)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('seatID',  'like', "%{$search}%")
                      ->orWhere('rowSeat', 'like', "%{$search}%")
                      ->orWhere('colSeat', 'like', "%{$search}%")
                      // Tìm kiếm kết hợp (Ví dụ nhập "A5" sẽ tìm ra ghế thuộc hàng A cột 5)
                      ->orWhereRaw("CONCAT(rowSeat, colSeat) LIKE ?", ["%{$search}%"])
                      // Tìm kiếm theo tên loại ghế (Thường, VIP...) thông qua liên kết quan hệ
                      ->orWhereHas('seatType', fn($t) => $t->where('seatTypeName', 'like', "%{$search}%"));
                });
            })
            ->orderBy('rowSeat') // Sắp xếp theo thứ tự chữ cái hàng (A-Z)
            ->orderBy('colSeat') // Sắp xếp theo số cột tăng dần (1-30)
            ->paginate(5)
            ->withQueryString();

        // 3. Tính toán kích thước lưới (Grid) thực tế dựa trên dữ liệu ghế hiện có
        $allSeatsInRoom = Seat::where('roomID', $roomID)->get(['rowSeat', 'colSeat']);
        $seatsRows      = $allSeatsInRoom->pluck('rowSeat')->unique()->count() ?: 1;
        $seatsCols      = (int) ($allSeatsInRoom->max('colSeat') ?: 1);

        // Đọc thông số cấu hình ma trận phòng từ Cache
        $grid = Cache::get("room_grid_{$roomID}");

        if ($grid && isset($grid['rows'], $grid['cols'])) {
            // Lấy giá trị lớn nhất giữa số liệu trong Cache và số liệu thực tế trong DB nhằm tránh mất form hiển thị
            $roomActualRows = max((int) $grid['rows'], $seatsRows);
            $roomActualCols = max((int) $grid['cols'], $seatsCols);
        } else {
            // Nếu phòng cũ chưa được tạo Cache cấu hình lưới -> Gán bằng giá trị thực tế của DB và lưu lại vĩnh viễn vào Cache
            $roomActualRows = $seatsRows;
            $roomActualCols = $seatsCols;
            Cache::forever("room_grid_{$roomID}", [
                'rows' => $roomActualRows,
                'cols' => $roomActualCols,
            ]);
        }

        return view('admins.manageCinema.seat.index', compact(
            'seats', 'room', 'rooms', 'seatTypes', 'roomID',
            'specialTypeIds', 'seatTypeNames',
            'roomActualRows', 'roomActualCols'
        ));
    }

    // ================= TẠO GHẾ (FORM) =================
    /**
     * Hiển thị giao diện form thêm mới một ghế bằng tay.
     */
    public function create()
    {
        $rooms     = ScreeningRoom::all();
        $seatTypes = SeatType::all();
        return view('admins.manageCinema.seat.create', compact('rooms', 'seatTypes'));
    }

    /**
     * Tiếp nhận dữ liệu từ Form tiêu chuẩn để thêm mới một ghế.
     */
    public function store(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào
        $request->validate([
            'rowSeat'    => 'required|string|max:2',
            'colSeat'    => 'required|integer|min:1|max:30',
            'roomID'     => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        // Chặn chỉnh sửa/thêm nếu phòng chiếu này hiện đang có lịch chiếu phim hoạt động
        if (ShowTime::where('roomID', $request->roomID)->exists()) {
            return back()->with('error', 'Phòng đang có suất chiếu');
        }

        // Kiểm tra xem vị trí Hàng + Cột này trong phòng đã được tạo trước đó chưa
        $exists = Seat::where('roomID',  $request->roomID)
            ->where('rowSeat', $request->rowSeat)
            ->where('colSeat', $request->colSeat)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ghế đã tồn tại');
        }

        Seat::create($request->all());
        return redirect()->route('seat.index')->with('success', 'Tạo ghế thành công');
    }

    // ================= TẠO GHẾ =================
    /**
     * Thêm mới một ghế trực tiếp từ sơ đồ bằng API AJAX mà không cần tải lại trang.
     */
    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'rowSeat'    => 'required|string|max:2',
            'colSeat'    => 'required|integer|min:1|max:30',
            'roomID'     => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        if (ShowTime::where('roomID', $validated['roomID'])->exists()) {
            return response()->json(['error' => 'Phòng đang có suất chiếu không thể tạo ghế'], 400);
        }

        $exists = Seat::where('roomID',  $validated['roomID'])
            ->where('rowSeat', $validated['rowSeat'])
            ->where('colSeat', $validated['colSeat'])
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Ghế đã tồn tại'], 400);
        }

        $seat = Seat::create($validated);

        /*
         * ĐẶC BIỆT: Khi thêm ghế mới qua AJAX, nếu tọa độ ghế mới vượt ra ngoài kích thước 
         * lưới (Grid) hiện tại lưu trong Cache, tiến hành cập nhật lại Cache để mở rộng sơ đồ.
         */
        $roomID    = (int) $validated['roomID'];
        $newRow    = strtoupper($validated['rowSeat']);
        $newCol    = (int) $validated['colSeat'];
        $newRowIdx = ord($newRow) - 64; // Hàm chuyển chữ cái sang số thứ tự tương ứng

        $grid = Cache::get("room_grid_{$roomID}") ?? ['rows' => 0, 'cols' => 0];
        $updated = false;

        // Nếu số thứ tự hàng của ghế mới lớn hơn số hàng đang lưu -> Mở rộng hàng
        if ($newRowIdx > (int) ($grid['rows'] ?? 0)) {
            $grid['rows'] = $newRowIdx;
            $updated = true;
        }
        // Nếu số cột mới lớn hơn số cột đang lưu -> Mở rộng cột
        if ($newCol > (int) ($grid['cols'] ?? 0)) {
            $grid['cols'] = $newCol;
            $updated = true;
        }
        // Lưu đè lại thông số kích thước lưới mới vào Cache vĩnh viễn
        if ($updated) {
            Cache::forever("room_grid_{$roomID}", $grid);
        }

        return response()->json($seat);
    }

    // ================= CẬP NHẬT NHIỀU GHẾ (FORM) =================
    /**
     * Thực hiện cập nhật loại ghế hàng loạt cho danh sách ID ghế gửi lên từ Form.
     */
    public function updateMultiple(Request $request)
    {
        $request->validate([
            'seatIDs'    => 'required|array|min:1',
            'seatIDs.*'  => 'exists:seats,seatID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        // Tìm ghế đầu tiên để xác định roomID của nhóm ghế này
        $firstSeat = Seat::whereIn('seatID', $request->seatIDs)->firstOrFail();
        $roomID    = $firstSeat->roomID;

        // Chặn cập nhật nếu phòng đang hoạt động suất chiếu
        if (ShowTime::where('roomID', $roomID)->exists()) {
            return redirect()
                ->route('seat.index', ['roomID' => $roomID])
                ->with('error', 'Phòng đang có suất chiếu');
        }

        // Tiến hành cập nhật loại ghế cho tất cả các ID nằm trong danh sách chọn
        Seat::whereIn('seatID', $request->seatIDs)
            ->update(['seatTypeID' => $request->seatTypeID]);

        return redirect()
            ->route('seat.index', ['roomID' => $roomID])
            ->with('success', 'Cập nhật thành công');
    }

    // ================= CHỈNH SỬA MỘT GHẾ (FORM) =================
    /**
     * Giao diện form sửa đổi vị trí/loại của 1 chiếc ghế cụ thể.
     */
    public function edit(string $id)
    {
        $seat      = Seat::findOrFail($id);
        $rooms     = ScreeningRoom::all();
        $seatTypes = SeatType::all();
        return view('admins.manageCinema.seat.edit', compact('seat', 'rooms', 'seatTypes'));
    }

    /**
     * Tiếp nhận dữ liệu chỉnh sửa của 1 chiếc ghế và lưu vào DB.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'rowSeat'    => 'required|string|max:2',
            'colSeat'    => 'required|integer|min:1|max:30',
            'roomID'     => 'required|exists:screening_rooms,roomID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        $seat = Seat::findOrFail($id);

        if (ShowTime::where('roomID', $seat->roomID)->exists()) {
            return back()->with('error', 'Phòng đang có suất chiếu');
        }

        $seat->update($request->all());
        return redirect()->route('seat.index')->with('success', 'Cập nhật ghế thành công');
    }

    // ================= XÓA GHẾ (FORM) =================
    /**
     * Xóa 1 chiếc ghế khỏi phòng chiếu thông qua request form chuẩn.
     */
    public function destroy(string $id)
    {
        $seat = Seat::findOrFail($id);

        if (ShowTime::where('roomID', $seat->roomID)->exists()) {
            return back()->with('error', 'Phòng đang có suất chiếu');
        }

        // Chặn xóa nếu chiếc ghế này đã được người dùng đặt và xuất Vé (Ticket) thành công
        if (Ticket::where('seatID', $id)->exists()) {
            return back()->with('error', 'Ghế đã có vé');
        }

        $seat->delete();
        return redirect()->route('seat.index')->with('success', 'Xóa ghế thành công');
    }

    // ================= XÓA GHẾ (AJAX) =================
    /**
     * Xóa ghế trực tiếp trên sơ đồ giao diện admin bằng AJAX mà không cần reload trang.
     */
    public function deleteAjax($id)
    {
        $seat = Seat::findOrFail($id);

        if (ShowTime::where('roomID', $seat->roomID)->exists()) {
            return response()->json(['error' => 'Phòng đang có suất chiếu không thể xóa'], 400);
        }

        if (Ticket::where('seatID', $id)->exists()) {
            return response()->json(['error' => 'Ghế đã có vé'], 400);
        }

        $seat->delete();
        return response()->json(['success' => true]);
    }

    // ================= LOAD GHẾ THEO PHÒNG (AJAX) =================
    /**
     * API trả về toàn bộ danh sách ghế của một phòng chiếu dạng JSON (Dùng hiển thị sơ đồ động).
     */
    public function getSeatsByRoom($roomID)
    {
        $seats = Seat::where('roomID', $roomID)
            ->orderBy('rowSeat')
            ->orderBy('colSeat')
            ->get();

        return response()->json($seats);
    }

    // ================= HOÁN ĐỔI VỊ TRÍ CỘT GHẾ (AJAX) =================
    /**
     * Hoán đổi vị trí hai số cột ghế cho nhau (Yêu cầu bắt buộc phải cùng nằm trên 1 hàng).
     */
    public function ajaxSwapCol(Request $request)
    {
        $request->validate([
            'seatID_a'  => 'required|integer|exists:seats,seatID',
            'colSeat_a' => 'required|integer|min:1',
            'seatID_b'  => 'required|integer|exists:seats,seatID',
            'colSeat_b' => 'required|integer|min:1',
        ]);

        $seatA = Seat::findOrFail($request->seatID_a);
        $seatB = Seat::findOrFail($request->seatID_b);

        if ($seatA->rowSeat !== $seatB->rowSeat) {
            return response()->json([
                'success' => false,
                'message' => 'Hai ghế phải cùng hàng mới có thể đổi vị trí.',
            ], 422);
        }

        // Sử dụng Database Transaction để đảm bảo tính toàn vẹn dữ liệu khi đổi chéo
        DB::transaction(function () use ($seatA, $seatB, $request) {
            // Bước đệm: Chuyển tạm thời cột ghế A về giá trị 0 nhằm tránh lỗi trùng khóa Unique (Hàng + Cột) nếu có trong DB
            $seatA->colSeat = 0;
            $seatA->save();
            
            // Gán giá trị cột cũ của ghế A cho ghế B
            $seatB->colSeat = $request->colSeat_a;
            $seatB->save();
            
            // Gán giá trị cột cũ của ghế B cho ghế A
            $seatA->colSeat = $request->colSeat_b;
            $seatA->save();
        });

        return response()->json([
            'success' => true,
            'message' => 'Hoán đổi vị trí ghế thành công.',
            'seatA'   => $seatA->fresh(), // Fresh() để tải lại dữ liệu mới nhất vừa lưu trong DB
            'seatB'   => $seatB->fresh(),
        ]);
    }

    // ================= HOÁN ĐỔI LOẠI GHẾ (AJAX) =================
    /**
     * Đổi chéo loại ghế (Ví dụ: Ghế A từ Thường -> VIP, Ghế B từ VIP -> Thường).
     */
    public function ajaxSwapType(Request $request)
    {
        $v = $request->validate([
            'seatID_a' => 'required|integer|exists:seats,seatID',
            'seatID_b' => 'required|integer|exists:seats,seatID',
        ]);

        $seatA = Seat::findOrFail($v['seatID_a']);
        $seatB = Seat::findOrFail($v['seatID_b']);

        if ($seatA->roomID !== $seatB->roomID) {
            return response()->json(['success' => false, 'message' => 'Hai ghế phải cùng phòng'], 422);
        }

        // Chặn hoán đổi nếu 1 trong 2 chiếc ghế này đã được xuất vé bán cho khách
        if (DB::table('tickets')->whereIn('seatID', [$seatA->seatID, $seatB->seatID])->exists()) {
            return response()->json(['success' => false, 'message' => 'Ghế đã có vé, không thể hoán đổi'], 422);
        }

        DB::transaction(function () use ($seatA, $seatB) {
            // Sử dụng tính năng List gán nhanh cấu trúc mảng để hoán đổi giá trị loại ghế
            [$seatA->seatTypeID, $seatB->seatTypeID] = [$seatB->seatTypeID, $seatA->seatTypeID];
            $seatA->save();
            $seatB->save();
        });

        return response()->json(['success' => true]);
    }

    // ================= CẬP NHẬT LOẠI GHẾ CHO 1 GHẾ (AJAX) =================
    /**
     * Thay đổi loại ghế đơn lẻ trên sơ đồ trực tuyến. Có xử lý logic sao lưu trạng thái khi đổi sang ghế Bảo Trì.
     */
    public function ajaxUpdateType(Request $request)
    {
        $validated = $request->validate([
            'seatID'     => 'required|integer|exists:seats,seatID',
            'seatTypeID' => 'required|integer|exists:seat_types,seatTypeID',
        ]);

        $seat              = Seat::findOrFail($validated['seatID']);
        $newTypeID         = (int) $validated['seatTypeID'];
        $maintenanceTypeID = $this->getMaintenanceTypeId();

        // Nếu loại ghế mới chọn KHÔNG PHẢI là ghế bảo trì, cần check xem ghế đã bán vé chưa
        if ($newTypeID !== $maintenanceTypeID) {
            if (DB::table('tickets')->where('seatID', $seat->seatID)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ghế đã có vé, không thể thay đổi loại.',
                ], 422);
            }
        }

        // LOGIC KHÓA GHẾ BẢO TRÌ:
        if ($newTypeID === $maintenanceTypeID) {
            // Nếu ghế hiện tại đang bình thường, ta sao lưu lại loại ghế gốc vào cột `originalSeatTypeID` trước khi gán loại bảo trì
            if ((int) $seat->seatTypeID !== $maintenanceTypeID) {
                $seat->originalSeatTypeID = $seat->seatTypeID;
            }
            $seat->seatTypeID = $newTypeID;
        } elseif ((int) $seat->seatTypeID === $maintenanceTypeID) {
            // Nếu ghế hiện tại ĐANG LÀ bảo trì mà được mở khóa -> Trả lại loại ghế gốc ban đầu của nó, nếu không có thì lấy loại mới chọn
            $seat->seatTypeID         = $seat->originalSeatTypeID ?? $newTypeID;
            $seat->originalSeatTypeID = null; // Xóa trạng thái lưu trữ tạm thời
        } else {
            // Các trường hợp chuyển đổi thông thường khác (Ví dụ: Thường sang VIP)
            $seat->seatTypeID = $newTypeID;
        }

        $seat->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật loại ghế thành công.',
            'seat'    => $seat->fresh(),
        ]);
    }

    // ================= CẬP NHẬT LOẠI GHẾ HÀNG LOẠT (AJAX) =================
    /**
     * Cập nhật loại ghế đồng loạt bằng AJAX (Bỏ qua các ghế đã bán vé để tránh lỗi hệ thống).
     */
    public function ajaxBatchUpdateType(Request $request)
    {
        $request->validate([
            'seatIDs'    => 'required|array|min:1',
            'seatIDs.*'  => 'integer|exists:seats,seatID',
            'seatTypeID' => 'required|integer|exists:seat_types,seatTypeID',
        ]);

        $newTypeID         = (int) $request->seatTypeID;
        $ids               = array_map('intval', $request->seatIDs);
        $maintenanceTypeID = $this->getMaintenanceTypeId();
        $isMaintenance     = $newTypeID === $maintenanceTypeID;
        $validSeatIds      = [];

        $selectedSeats = Seat::whereIn('seatID', $ids)->get();

        // Lọc qua danh sách: Chỉ cho phép các ghế chưa bán vé tham gia chỉnh sửa (Trừ trường hợp khóa bảo trì đột xuất)
        foreach ($selectedSeats as $seat) {
            if (!$isMaintenance && DB::table('tickets')->where('seatID', $seat->seatID)->exists()) {
                continue; // Bỏ qua ghế đã có vé
            }
            $validSeatIds[] = $seat->seatID;
        }

        $updated = count($validSeatIds);
        $skipped = count($ids) - $updated;

        if ($updated === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không có ghế nào hợp lệ để cập nhật (ghế có vé bị bỏ qua).',
            ]);
        }

        // Nếu chuyển hàng loạt sang ghế Bảo trì -> Cần chạy vòng lặp để sao lưu `originalSeatTypeID` cho từng ghế lẻ
        if ($isMaintenance) {
            foreach ($selectedSeats->whereIn('seatID', $validSeatIds) as $seat) {
                if ((int) $seat->seatTypeID !== $maintenanceTypeID) {
                    $seat->originalSeatTypeID = $seat->seatTypeID;
                }
                $seat->seatTypeID = $newTypeID;
                $seat->save();
            }
        } else {
            // Nếu là loại thông thường -> Sử dụng câu lệnh Bulk Update để đạt hiệu năng tối đa
            Seat::whereIn('seatID', $validSeatIds)->update(['seatTypeID' => $newTypeID]);
        }

        $typeName = SeatType::find($newTypeID)?->seatTypeName ?? 'loại mới';
        $msg      = "Đã cập nhật {$updated} ghế sang {$typeName}.";
        if ($skipped > 0) $msg .= " Bỏ qua {$skipped} ghế đã có vé.";

        return response()->json(['success' => true, 'updated' => $updated, 'message' => $msg]);
    }

    // ================= GIAO DIỆN SỬA ĐỒNG LOẠT (FORM CHUYỂN TRANG) =================
    /**
     * Hiển thị một form sửa đổi tập trung dành cho danh sách chuỗi các ID ghế ngăn cách bởi dấu phẩy.
     */
    public function editMultiple(Request $request)
    {
        $ids       = explode(',', $request->seatIDs); // Bẻ nhỏ chuỗi "1,2,3" thành mảng [1, 2, 3]
        $seats     = Seat::whereIn('seatID', $ids)->get();
        $seatTypes = SeatType::all();
        $rooms     = ScreeningRoom::all();
        $roomID    = $seats->first()?->roomID;

        return view('admins.manageCinema.seat.edit-multiple', compact(
            'seats', 'seatTypes', 'rooms', 'roomID'
        ));
    }

    /**
     * Xử lý lưu loại ghế sửa đồng loạt (Chỉ cập nhật cho các ghế chưa có vé đặt).
     */
    public function updateType(Request $request)
    {
        $request->validate([
            'seatIDs'    => 'required|array|min:1',
            'seatIDs.*'  => 'exists:seats,seatID',
            'seatTypeID' => 'required|exists:seat_types,seatTypeID',
        ]);

        $firstSeat = Seat::whereIn('seatID', $request->seatIDs)->first();

        if (!$firstSeat) {
            return response()->json(['error' => 'Không tìm thấy ghế'], 404);
        }

        if (ShowTime::where('roomID', $firstSeat->roomID)->exists()) {
            return response()->json(['error' => 'Phòng đang có suất chiếu không thể sửa'], 400);
        }

        // Lọc lấy danh sách ID các ghế thực sự an toàn (Không nằm trong bảng tickets) bằng Subquery loại trừ
        $validSeatIDs = Seat::whereIn('seatID', $request->seatIDs)
            ->whereNotIn('seatID', function ($q) {
                $q->select('seatID')->from('tickets');
            })
            ->pluck('seatID');

        $updated = Seat::whereIn('seatID', $validSeatIDs)
            ->update(['seatTypeID' => $request->seatTypeID]);

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'skipped' => count($request->seatIDs) - $updated, // Số ghế bị giữ nguyên vì đã bán vé
            'roomID'  => $firstSeat->roomID,
        ]);
    }

    // ================= TRANG ĐẶT GHẾ DÀNH CHO KHÁCH HÀNG (CLIENT) =================
    /**
     * Hiển thị sơ đồ đặt ghế cho khách hàng khi chọn một Suất Chiếu (Showtime) cụ thể ngoài trang chủ.
     */
    public function selectSeat($showtimeID)
    {
        $showtime = ShowTime::findOrFail($showtimeID);

        // Lấy tất cả ghế thuộc phòng của suất chiếu đó để vẽ sơ đồ ma trận
        $seats = Seat::where('roomID', $showtime->roomID)
            ->orderBy('rowSeat')
            ->orderBy('colSeat')
            ->get();

        // Lấy danh sách danh tính các ghế ĐÃ ĐƯỢC MUA thuộc suất chiếu này để chuyển sang trạng thái Disable (Khóa không cho click)
        $bookedSeats = Ticket::where('showTimeID', $showtimeID)
            ->pluck('seatID')
            ->toArray();

        return view('system.seatSelect', compact('seats', 'showtime', 'bookedSeats'));
    }
}