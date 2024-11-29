<?php

namespace App\Models\BookingApproval;

use App\Models\Booking\BookingModel;
use App\Models\User;

trait BookingApprovalRelationship
{
    public function booking()
    {
        return $this->belongsTo(BookingModel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}