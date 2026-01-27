<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecieRequest;
use App\Models\Specie;
use Illuminate\Database\QueryException;
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

public function store(StoreSpecieRequest $request)
    {
        //
        try {
            $row = Specie::create($request->all());

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
                        'fish_name' => $request->input('fish_name') // Visszaküldhetjük, mi volt a hibás
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
