<?php

namespace App\Models\Booking;

use App\Models\BookingApproval\BookingApprovalModel;
use App\Models\BookingDetail\BookingDetailModel;
use App\Models\CoworkPlan\CoworkPlanModel;
use App\Models\User;

trait BookingRelationship
{
    public function plan()
    {
        return $this->belongsTo(CoworkPlanModel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasOne(BookingDetailModel::class, 'booking_id', 'id');
    }

    public function approval()
    {
        return $this->hasOne(BookingApprovalModel::class, 'booking_id', 'id');
    }
}
