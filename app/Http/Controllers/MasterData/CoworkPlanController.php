<?php

namespace App\Http\Controllers\MasterData;

use App\Helpers\ResponseModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\CoworkPlan\Store;
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
