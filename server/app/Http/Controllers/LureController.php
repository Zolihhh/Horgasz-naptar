<?php

namespace App\Http\Controllers;

use App\Models\Lure  as CurrentModel;
use App\Http\Requests\StoreLureRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateLureRequest as UpdateCurrentModelRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
class LureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
       return $this->apiResponse(function () use ($id) {
            return CurrentModel::findOrFail($id);
        });
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $row = CurrentModel::findOrFail($id);
            $row->update($request->validated());
            return $row;
        });
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(int $id)
    {
        return $this->apiResponse(function () use ($id) {
            CurrentModel::findOrFail($id)->delete();
            return ['id' => $id];
        });
    }
}
