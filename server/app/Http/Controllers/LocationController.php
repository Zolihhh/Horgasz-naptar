<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest as StoreCurrentModelRequest;
use App\Models\Location as CurrentModel;;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    //    public function index()
//     {
//         return response()->json([
//             'message' => 'OK',
//             'data' => Location::all()
//         ], 200, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
//     }

    public function index()
    {
       return $this->apiResponse(
            function () {
                return CurrentModel::all();
            }
        );
    }

    // public function store(Request $request)
    // {
    //     $row = Location::create($request->all());

    //     return response()->json([
    //         'message' => 'Location created',
    //         'data' => $row
    //     ], 201, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    // }

    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(
            function () use ($request) {
                return CurrentModel::create($request->validated());
            }
        );
    }

}
