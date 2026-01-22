<?php

namespace App\Http\Controllers;

use App\Models\FishCatch;
use App\Http\Requests\StoreFishCatchRequest;
use App\Http\Requests\UpdateFishCatchRequest;
use Illuminate\Database\QueryException;

class FishCatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $fishCatches = FishCatch::with('catchLog')
                ->whereHas('catchLog', function ($query) {
                    $query->where('userid', auth()->id());
                })
                ->get();

            $status = 200;
            $data = [
                'message' => 'OK',
                'data' => $fishCatches
            ];
        } catch (\Exception $e) {
            $status = 500;
            $data = [
                'message' => "Server error {$e->getCode()}",
                'data' => []
            ];
        }

        return response()->json($data, $status, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFishCatchRequest $request)
    {
        try {
            $fishCatch = FishCatch::create([
                'catch_log_id' => $request->catch_log_id,
                'species_id' => $request->species_id,
                'weight' => $request->weight,
                'length' => $request->length,
                'lure_id' => $request->lure_id,
                'catch_time' => $request->catch_time,
            ]);

            $status = 201;
            $data = [
                'message' => 'Fish catch sikeresen mentve!',
                'data' => $fishCatch
            ];
        } catch (QueryException $e) {
            $status = 400;
            $data = [
                'message' => "Insert error: {$e->getMessage()}",
                'data' => []
            ];
        }

        return response()->json($data, $status, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $fishCatch = FishCatch::with('catchLog')->find($id);

        if ($fishCatch) {
            $status = 200;
            $data = [
                'message' => 'OK',
                'data' => $fishCatch
            ];
        } else {
            $status = 404;
            $data = [
                'message' => "Not found id: $id",
                'data' => null
            ];
        }

        return response()->json($data, $status, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFishCatchRequest $request, int $id)
    {
        $fishCatch = FishCatch::find($id);

        if ($fishCatch) {
            $fishCatch->update($request->validated());

            $status = 200;
            $data = [
                'message' => 'Fish catch frissítve!',
                'data' => $fishCatch
            ];
        } else {
            $status = 404;
            $data = [
                'message' => "Patch error. Not found id: $id",
                'data' => null
            ];
        }

        return response()->json($data, $status, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $fishCatch = FishCatch::find($id);

        if ($fishCatch) {
            $fishCatch->delete();

            $status = 200;
            $data = [
                'message' => 'Fish catch törölve!',
                'data' => ['id' => $id]
            ];
        } else {
            $status = 404;
            $data = [
                'message' => "Delete error. Not found id: $id",
                'data' => null
            ];
        }

        return response()->json($data, $status, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }
}
