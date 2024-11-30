<?php

namespace App\Http\Controllers\MasterData;

use App\Helpers\ResponseModel;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\Coworking\Store;
use App\Models\Coworking\CoworkingModel;
use Illuminate\Http\Request;

class CoworkingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = CoworkingModel::paginate(20);

        return ResponseModel::success($results);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request)
    {
        $data = $request->all();

        $results = CoworkingModel::create($data);

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
