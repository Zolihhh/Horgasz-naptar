<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesUploadedImages;
use App\Http\Requests\StoreLocationRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateLocationRequest as UpdateCurrentModelRequest;
use App\Models\Location as CurrentModel;

class LocationController extends Controller
{
    use HandlesUploadedImages;

    public function index()
    {
        return $this->apiResponse(function () {
            return CurrentModel::all();
        });
    }

    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $payload = $request->validated();

            if ($request->hasFile('photo')) {
                $payload['photo'] = $this->storeUploadedImage($request->file('photo'), 'location');
            }

            unset($payload['existing_photo']);

            return CurrentModel::create($payload);
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
            $payload = $request->validated();

            if ($request->hasFile('photo')) {
                $payload['photo'] = $this->storeUploadedImage($request->file('photo'), 'location');
                $this->deleteManagedImage($row->photo, 'location');
            } else {
                $payload['photo'] = $payload['existing_photo'] ?? $row->photo;
            }

            unset($payload['existing_photo']);

            $row->update($payload);

            return $row->fresh();
        });
    }

    public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            $row = CurrentModel::findOrFail($id);
            $this->deleteManagedImage($row->photo, 'location');
            $row->delete();

            return ['id' => $id];
        });
    }

    public function photo(string $filename)
    {
        return $this->imageFileResponse($filename);
    }
}
