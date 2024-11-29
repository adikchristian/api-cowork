<?php

namespace App\Models\BookingDetail;

use App\Models\Booking\BookingModel;

trait BookingDetailRelationship
{
    public function booking()
    {
        return $this->belongsTo(BookingModel::class);
    }
}