<?php

namespace App\Http\Controllers\Booking;

use App\Helpers\ResponseModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\BookingDetail\Store;
use App\Models\Booking\BookingModel;
use App\Models\BookingDetail\BookingDetailModel;
use Illuminate\Http\Request;

class BookingDetailController extends Controller
{
    public function store(Store $request)
    {
        $data = $request->all();
        $booking = BookingModel::where(['status' => 'pending'])
            ->find($data['booking_id']);
        $authUser = auth('api')->user();

        if(!$booking){
            return ResponseModel::error('Booking Not Found', 404);
        }

        if ($authUser->id != $booking->user_id) {
            return ResponseModel::error('You are not allowed to do this action', 403);
        }
        $data['file'] = $request->file('file')->store('booking');
        $detail = BookingDetailModel::create($data);

        return ResponseModel::success($detail);
    }
}
