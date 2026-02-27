<?php

namespace App\Http\Controllers;

use App\Models\FishCatch as CurrentModel;
use App\Http\Requests\StoreFishCatchRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateFishCatchRequest as UpdateCurrentModelRequest;
use Illuminate\Database\QueryException;

class FishCatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->apiResponse(
            function () {
                return CurrentModel::all();
            }
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(
            function () use ($request) {
                return CurrentModel::create($request->validated());
            }
        );
    }
    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            return CurrentModel::findOrFail($id);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrentModelRequest $request, int $id)
    {
         $fishCatch = CurrentModel::findOrFail($id);

    $fishCatch->update($request->validated());

    return response()->json([
        'message' => 'updated',
        'data' => $fishCatch
    ], 200, options: JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            CurrentModel::findOrFail($id)->delete();
            return ['id' => $id];

        });
    }
}
