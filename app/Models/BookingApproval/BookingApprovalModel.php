<?php

namespace App\Models\BookingApproval;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingApprovalModel extends Model
{
    use SoftDeletes, BookingApprovalRelationship;

    protected $table = 'booking_approvals';

    protected $fillable = [
        'booking_id',
        'user_id',
        'status',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
