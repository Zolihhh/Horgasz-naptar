<?php

namespace App\Http\Controllers;

use App\Models\CatchLog;
use App\Models\FishCatch;
use App\Http\Requests\StoreCatchLogRequest;
use App\Http\Requests\UpdateCatchLogRequest;
use Illuminate\Database\QueryException;

class CatchLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $catchLogs = CatchLog::with('fishCatches')
                ->where('userid', auth()->id())
                ->get();

            $status = 200;
            $data = [
                'message' => 'OK',
                'data' => $catchLogs
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
    public function store(StoreCatchLogRequest $request)
    {
        try {
            $catchLog = CatchLog::create([
                'userid' => auth()->id(),
                'fishing_lake_id' => $request->fishing_lake_id,
                'comment' => $request->comment,
                'fishing_start' => $request->fishing_start,
                'fishing_end' => $request->fishing_end,
            ]);

            if ($request->has('fish_catches')) {
                foreach ($request->fish_catches as $catch) {
                    $catchLog->fishCatches()->create([
                        'species_id' => $catch['species_id'],
                        'weight' => $catch['weight'],
                        'length' => $catch['length'],
                        'lure_id' => $catch['lure_id'],
                        'catch_time' => $catch['catch_time'],
                    ]);
                }
            }

            $status = 201;
            $data = [
                'message' => 'Catch log sikeresen mentve!',
                'data' => $catchLog->load('fishCatches')
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
        $catchLog = CatchLog::with('fishCatches')->find($id);

        if ($catchLog) {
            $status = 200;
            $data = [
                'message' => 'OK',
                'data' => $catchLog
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
    public function update(UpdateCatchLogRequest $request, int $id)
    {
        $catchLog = CatchLog::find($id);

        if ($catchLog) {
            $catchLog->update($request->validated());

            $status = 200;
            $data = [
                'message' => 'Catch log frissítve!',
                'data' => $catchLog
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
        $catchLog = CatchLog::find($id);

        if ($catchLog) {
            $catchLog->fishCatches()->delete();
            $catchLog->delete();

            $status = 200;
            $data = [
                'message' => 'Catch log törölve!',
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
