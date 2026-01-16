<?php

namespace App\Http\Controllers;

use App\Models\CatchLog;
use App\Http\Requests\StoreCatchLogRequest;
use App\Http\Requests\UpdateCatchLogRequest;

class CatchLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCatchLogRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CatchLog $catchLog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCatchLogRequest $request, CatchLog $catchLog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CatchLog $catchLog)
    {
        //
    }
}
