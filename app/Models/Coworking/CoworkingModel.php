<?php

namespace App\Models\Coworking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoworkingModel extends Model
{
    use SoftDeletes;

    protected $table = 'coworkings';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'url',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
