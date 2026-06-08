<?php

namespace App\Services;

use App\Models\Admin\Invoice;
use App\Models\Admin\Seat;
use App\Models\Admin\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingService
{
    public function finalizeFromSession(array $invoiceData, int $paymentId, int $customerId): Invoice
    {
        $showTimeId = $invoiceData['showtime_id'] ?? null;
        $seatIds = $invoiceData['seat_ids'] ?? [];

        if (!$showTimeId || empty($seatIds)) {
            throw new RuntimeException('Thiếu dữ liệu suất chiếu hoặc ghế.');
        }

        return DB::transaction(function () use ($customerId, $paymentId, $showTimeId, $seatIds) {
            $seats = Seat::with('seatType')
                ->whereIn('seatID', $seatIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('seatID');

            if ($seats->count() !== count($seatIds)) {
                throw new RuntimeException('Một hoặc nhiều ghế không còn tồn tại.');
            }

            $existingTickets = Ticket::where('showTimeID', $showTimeId)
                ->whereIn('seatID', $seatIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('seatID');

            foreach ($seatIds as $seatId) {
                $ticket = $existingTickets->get($seatId);

                if ($ticket && strtolower((string) $ticket->status) === 'booked') {
                    $seat = $seats->get($seatId);
                    $seatCode = $seat ? $seat->rowSeat . $seat->colSeat : '#' . $seatId;

                    throw new RuntimeException("Ghế {$seatCode} đã được đặt.");
                }
            }

            $totalAmount = collect($seatIds)->sum(function ($seatId) use ($seats) {
                return (float) ($seats->get($seatId)?->seatType?->price ?? 0);
            });

            $invoice = Invoice::create([
                'createDate' => Carbon::now(),
                'totalAmount' => $totalAmount,
                'customerID' => $customerId,
                'adminID' => null,
                'paymentID' => $paymentId,
            ]);

            foreach ($seatIds as $seatId) {
                $price = (float) ($seats->get($seatId)?->seatType?->price ?? 0);
                $ticket = $existingTickets->get($seatId);

                if ($ticket) {
                    $ticket->update([
                        'price' => $price,
                        'status' => 'booked',
                        'invoiceID' => $invoice->invoiceID,
                    ]);

                    continue;
                }

                Ticket::create([
                    'price' => $price,
                    'status' => 'booked',
                    'showTimeID' => $showTimeId,
                    'seatID' => $seatId,
                    'invoiceID' => $invoice->invoiceID,
                ]);
            }

            return $invoice;
        });
    }
}
