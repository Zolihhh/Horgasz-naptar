<?php

namespace App\Http\Controllers;

use App\Models\Specie;
use Illuminate\Http\Request;

class SpecieController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'OK',
            'data' => Specie::all()
        ], 200, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }

    public function store(Request $request)
    {
        $row = Specie::create($request->all());

        return response()->json([
            'message' => 'Specie created',
            'data' => $row
        ], 201, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    }
}
