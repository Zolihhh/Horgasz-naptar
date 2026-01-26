<?php

namespace App\Http\Controllers;

use App\Models\Lure;
use App\Http\Requests\StoreLureRequest;
use App\Http\Requests\UpdateLureRequest;
use Illuminate\Http\Request;
class LureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        return response()->json([
            'message' => 'OK',
            'data' => Lure::all()
        ], 200, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    public function store(Request $request)
    {
        $row = Lure::create($request->all());

        return response()->json([
            'message' => 'Lure created',
            'data' => $row
        ], 201, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
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
