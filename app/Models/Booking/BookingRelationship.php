<?php

namespace App\Models\Booking;

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
}