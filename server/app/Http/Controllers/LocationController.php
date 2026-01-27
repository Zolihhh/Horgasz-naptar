<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Models\Location;
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
        try {
            //code...
            $rows = Location::all();
            // $sql ="SELECT * FROM products";
            // $rows = DB::select($sql);
            $status = 200;
            $data = [
                'message' => 'OK',
                'data' => $rows
            ];
        } catch (\Exception $e) {
            //throw $th;
            $status = 500;
            $data = [
                'message' => "Server error {$e->getCode()}",
                'data' => $rows
            ];
        }

        return response()->json($data, $status, options: JSON_UNESCAPED_UNICODE);
    }


    // public function store(Request $request)
    // {
    //     $row = Location::create($request->all());

    //     return response()->json([
    //         'message' => 'Location created',
    //         'data' => $row
    //     ], 201, ['json_encode_options' => JSON_UNESCAPED_UNICODE]);
    // }

    public function store(StoreLocationRequest $request)
    {
        //
        try {
            $row = Location::create($request->all());

            $data = [
                'message' => 'ok',
                'data' => $row
            ];
            // Sikeres válasz: 201 Created kód ajánlott új erőforrás létrehozásakor
            return response()->json($data, 201, options: JSON_UNESCAPED_UNICODE);
        } catch (QueryException $e) {
            // Ellenőrizzük, hogy ez egy "Duplicate entry for key" hiba-e (MySQL hibakód: 23000 vagy 1062)
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
                $data = [
                    'message' => 'Insert error: The given name already exists, please choose another one',
                    'data' => [
                        'FishingLakeName' => $request->input('FishingLakeName') // Visszaküldhetjük, mi volt a hibás
                    ]
                ];
                // Kliens hiba, ami jelzi a kérés érvénytelenségét
                return response()->json($data, 409, options: JSON_UNESCAPED_UNICODE); // 409 Conflict ajánlott
            }

            // Ha nem ez a hiba volt, dobjuk tovább az eredeti kivételt, vagy kezeljük másképp
            throw $e;
        }
    }

}
