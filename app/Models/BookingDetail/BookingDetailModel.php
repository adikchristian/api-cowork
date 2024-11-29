<?php

namespace App\Models\BookingDetail;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingDetailModel extends Model
{
    use SoftDeletes, BookingDetailRelationship;

    protected $table = 'booking_details';

    protected $fillable = [
        'booking_id',
        'date',
        'file',
    ];

    protected $hidden = [
        'deleted_at',
        'updated_at',
        'created_at',
    ];
}
