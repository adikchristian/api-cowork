<?php

namespace App\Http\Controllers\MasterData;

use App\Helpers\ResponseModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\CoworkPlan\Store;
use App\Http\Requests\MasterData\CoworkPlan\Update;
use App\Models\CoworkPlan\CoworkPlanModel;
use Illuminate\Http\Request;

class CoworkPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = CoworkPlanModel::with('coworking')->paginate(20);

        return ResponseModel::success($results);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request)
    {
        $data = $request->all();

        $results = CoworkPlanModel::create($data);

        return ResponseModel::success($results);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $results = CoworkPlanModel::with('coworking')->find($id);

        if (!$results) {
            return ResponseModel::error('Coworking Plan Not Found', 404);
        }

        return ResponseModel::success($results);
    }

    public function showCoworking(string $coworkingID)
    {
        $results = CoworkPlanModel::with('coworking')
        ->where('coworking_id', $coworkingID)
        ->paginate(20);

        return ResponseModel::success($results);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, string $id)
    {
        $data = $request->all();

        $results = CoworkPlanModel::find($id);

        if (!$results) {
            return ResponseModel::error('Coworking Plan Not Found', 404);
        }

        $results->update($data);

        return ResponseModel::success($results);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $results = CoworkPlanModel::find($id);

        if (!$results) {
            return ResponseModel::error('Coworking Plan Not Found', 404);
        }

        $results->delete();

        return ResponseModel::success(\null,'Coworking Plan deleted successfully');
    }
}
