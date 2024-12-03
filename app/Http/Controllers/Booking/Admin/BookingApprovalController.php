<?php

namespace App\Http\Controllers\Booking\Admin;

use App\Helpers\ResponseModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingApproval\Admin\Store;
use App\Models\Booking\BookingModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingApprovalController extends Controller
{
    public function store(Store $request)
    {
        $data = $request->all();
        $data['user_id'] = auth('api')->user()->id;

        $booking = BookingModel::with(['detail', 'approval'])
            ->find($data['booking_id']);

        if (!$booking) {
            return ResponseModel::error('Booking Not Found', 404);
        }

        if ($booking->status !== "pending") {
            return ResponseModel::error('Booking Status Not Pending', 400);
        }

        if ($booking->detail == null) {
            return ResponseModel::error('Booking not have confirmation', 404);
        }

        try {
            DB::transaction(function () use ($data, $booking) {
                $booking->approval()->create($data);
                $booking->update([
                    'status' => $data['status'],
                ]);
            });
            return ResponseModel::success(\null, 'Approve Booking Success');
        } catch (\Throwable $th) {
            return ResponseModel::error(
                'Internal Server Error',
                500,
                $th->getMessage()
            );
        }
    }
}
