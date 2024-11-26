<?php

namespace App\Models\CoworkPlan;

use App\Models\Coworking\CoworkingModel;

trait CoworkPlanRelationship
{
    public function coworking()
    {
        return $this->belongsTo(CoworkingModel::class);
    }
}