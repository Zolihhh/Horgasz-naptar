<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
   public function index()
    {
        return response()->json([
            'message' => 'OK',
            'data' => Location::all()
        ], 200, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }


    public function store(Request $request)
    {
        $row = Location::create($request->all());

        return response()->json([
            'message' => 'Location created',
            'data' => $row
        ], 201, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }
}
