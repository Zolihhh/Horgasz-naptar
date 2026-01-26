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

    public function show(int $id)
    {
        $row = Location::find($id);

        return $row
            ? response()->json(['message' => 'OK', 'data' => $row], 200, ['json_encode_options' => JSON_UNESCAPED_UNICODE])
            : response()->json(['message' => "Not found id: $id", 'data' => null], 404, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    public function store(Request $request)
    {
        $row = Location::create($request->all());

        return response()->json([
            'message' => 'Location created',
            'data' => $row
        ], 201, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    public function update(Request $request, int $id)
    {
        $row = Location::find($id);

        if (!$row) {
            return response()->json([
                'message' => "Update error. Not found id: $id",
                'data' => null
            ], 404, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
        }

        $row->update($request->all());

        return response()->json([
            'message' => 'Location updated',
            'data' => $row
        ], 200, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    public function destroy(int $id)
    {
        Location::destroy($id);

        return response()->json([
            'message' => 'Location deleted',
            'data' => ['id' => $id]
        ], 200, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }
}
