<?php

namespace App\Http\Controllers\Booking;

use App\Helpers\ResponseModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\Store;
use App\Models\Booking\BookingModel;
use App\Models\CoworkPlan\CoworkPlanModel;
use Illuminate\Http\Request;

class BookingController extends Controller
{

    public function index()
    {
        $user = \auth('api')->user();
        $bookings = BookingModel::where('user_id', $user->id)
        ->paginate(20);
        
        return ResponseModel::success($bookings);
    }

    public function show($code)
    {
        $user = \auth('api')->user();
        $booking = BookingModel::where('user_id', $user->id)
        ->where('code', $code)
        ->first();

        if (!$booking) {
            return ResponseModel::error('Booking Not Found', 404);
        }
        
        return ResponseModel::success($booking);
    }

    public function store(Store $request)
    {
        $data = $request->all();
        $data['code'] = $this->generateCode($data['user_id']);

        $coworkPlan = CoworkPlanModel::find($data['cowork_plan_id']);
        $data['price'] = $coworkPlan->price;

        $booking = BookingModel::create($data);
        return ResponseModel::success($booking);

    }

    public function generateCode($userId)
    {
        $bookingByUser = BookingModel::where('user_id', $userId)->count();
        $bookingByUser = $bookingByUser + 1;

        $code = \date('ymd').$userId.$bookingByUser;
        return $code;
    }
}
