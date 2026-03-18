<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSpecieRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateSpecieRequest as UpdateCurrentModelRequest;
use App\Models\Specie as CurrentModel;

class SpecieController extends Controller
{
    public function index()
    {
        return $this->apiResponse(function () {
            return CurrentModel::all();
        });
    }

    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            return CurrentModel::create($request->validated());
        });
    }

    public function show(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            return CurrentModel::findOrFail($id);
        });
    }

    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $row = CurrentModel::findOrFail($id);
            $row->update($request->validated());
            return $row;
        });
    }

    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            CurrentModel::findOrFail($id)->delete();
            return ['id' => $id];
        });
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
