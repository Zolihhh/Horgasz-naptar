<?php

namespace App\Http\Controllers;

use App\Models\CatchLog;
use App\Models\FishCatch;
use App\Http\Requests\StoreCatchLogRequest;
use App\Http\Requests\UpdateCatchLogRequest;

class CatchLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Az aktuális felhasználó CatchLog-jai fishCatches-rel együtt
        $catchLogs = CatchLog::with('fishCatches')->where('userid', auth()->id())->get();
        return view('catch_logs.index', compact('catchLogs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCatchLogRequest $request)
    {
        // 1️⃣ CatchLog létrehozása
        $catchLog = CatchLog::create([
            'userid' => auth()->id(),
            'fishing_lake_id' => $request->fishing_lake_id,
            'comment' => $request->comment,
            'fishing_start' => $request->fishing_start,
            'fishing_end' => $request->fishing_end,
        ]);

        // 2️⃣ FishCatch-ek mentése, ha a felhasználó adott meg
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

        return redirect()->back()->with('success', 'Catch log sikeresen mentve!');
    }

    /**
     * Display the specified resource.
     */
    public function show(CatchLog $catchLog)
    {
        $catchLog->load('fishCatches');
        return view('catch_logs.show', compact('catchLog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCatchLogRequest $request, CatchLog $catchLog)
    {
        // CatchLog frissítése
        $catchLog->update($request->validated());

        // Itt lehetne frissíteni a fish_catches-eket is, ha akarod
        return redirect()->back()->with('success', 'Catch log frissítve!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CatchLog $catchLog)
    {
        // Kapcsolódó FishCatch-ek törlése
        $catchLog->fishCatches()->delete();

        // CatchLog törlése
        $catchLog->delete();

        return redirect()->back()->with('success', 'Catch log törölve!');
    }
}
