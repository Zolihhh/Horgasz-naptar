<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecieRequest as StoreCurrentModelRequest;
use App\Models\Specie as CurrentModel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SpecieController extends Controller
{
    public function index()
    {
       return $this->apiResponse(
            function () {
                return CurrentModel::all();
            }
        );
    }

public function store(StoreCurrentModelRequest $request)
    {
         return $this->apiResponse(
            function () use ($request) {
                return CurrentModel::create($request->validated());
            }
        );
        
    }

    public function photo(string $filename)
    {
        $safeFilename = basename($filename);
        $path = database_path('images/' . $safeFilename);

        if (!is_file($path)) {
            abort(404, 'A kep nem talalhato.');
        }

        return response()->file($path);
    }
}
