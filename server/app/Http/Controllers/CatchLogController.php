<?php

namespace App\Http\Controllers;

use App\Models\CatchLog;
use App\Models\FishCatch;
use App\Http\Requests\StoreCatchLogRequest;
use App\Http\Requests\UpdateCatchLogRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

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
$user = $request->user(); // token alapján

if (!$user) {
    return response()->json([
        'message' => 'Unauthorized. Nincs bejelentkezett user.',
        'data' => []
    ], 401);
}

    // CatchLog létrehozása
    $catchLog = CatchLog::create([
        'userid' => $user->id,
        'fishing_lake_id' => $request->fishing_lake_id,
        'comment' => $request->comment,
        'fishing_start' => $request->fishing_start,
        'fishing_end' => $request->fishing_end,
    ]);

    // FishCatches létrehozása
    foreach ($request->fish_catches as $catch) {
        $catchLog->fishCatches()->create($catch);
    }

    return response()->json([
        'message' => 'Catch log és fish catches sikeresen létrehozva!',
        'data' => $catchLog->load('fishCatches')
    ], 201, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
}

    /**
     * Display the specified resource.
     */
public function show(int $id)
    {
        $row = CatchLog::find($id);
        if ($row) {
            # code...
            $status = 200;
            $data = [
                'message' => 'OK',
                'data' => $row
            ];
        } else {
            # code...
            $status = 404;
            $data = [
                'message' => "Not found id: $id",
                'data' => null
            ];
        }
        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateCatchLogRequest $request, int $id)
    // {
    //     $catchLog = CatchLog::find($id);

    //     if ($catchLog) {
    //         $catchLog->update($request->validated());

    //         $status = 200;
    //         $data = [
    //             'message' => 'Catch log frissítve!',
    //             'data' => $catchLog
    //         ];
    //     } else {
    //         $status = 404;
    //         $data = [
    //             'message' => "Patch error. Not found id: $id",
    //             'data' => null
    //         ];
    //     }

    //     return response()->json($data, $status, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(int $id)
    // {
    //     $catchLog = CatchLog::find($id);

    //     if ($catchLog) {
    //         $catchLog->fishCatches()->delete();
    //         $catchLog->delete();

    //         $status = 200;
    //         $data = [
    //             'message' => 'Catch log törölve!',
    //             'data' => ['id' => $id]
    //         ];
    //     } else {
    //         $status = 404;
    //         $data = [
    //             'message' => "Delete error. Not found id: $id",
    //             'data' => null
    //         ];
    //     }

    //     return response()->json($data, $status, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    // }
}
