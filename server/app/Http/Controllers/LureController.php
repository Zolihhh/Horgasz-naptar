<?php

namespace App\Http\Controllers;

use App\Models\Lure;
use App\Http\Requests\StoreLureRequest;
use App\Http\Requests\UpdateLureRequest;

class LureController extends Controller
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
    public function store(StoreLureRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Lure $lure)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLureRequest $request, Lure $lure)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lure $lure)
    {
        //
    }
}
