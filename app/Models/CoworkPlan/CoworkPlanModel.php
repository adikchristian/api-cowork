<?php

namespace App\Models\CoworkPlan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoworkPlanModel extends Model
{
    use SoftDeletes, CoworkPlanRelationship;

    protected $table = 'cowork_plans';

    protected $fillable = [
        'coworking_id',
        'code',
        'name',
        'price',
        'benefit',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
