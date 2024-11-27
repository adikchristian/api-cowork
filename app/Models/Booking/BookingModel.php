<?php

namespace App\Models\Booking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingModel extends Model
{
    use SoftDeletes, BookingRelationship;

    protected $table = 'bookings';

    protected $fillable = [
        'code',
        'cowork_plan_id',
        'user_id',
        'date',
        'price',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
