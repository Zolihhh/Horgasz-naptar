<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateLocationRequest as UpdateCurrentModelRequest;
use App\Models\Location as CurrentModel;

class LocationController extends Controller
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
}
