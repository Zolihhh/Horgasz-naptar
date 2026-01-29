<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCatchLogRequest;
use App\Models\CatchLog as CurrentModel;
use App\Models\FishCatch;
use App\Http\Requests\StoreCatchLogRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateCatchLogRequest as UpdateCurrentModelReques;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class CatchLogController extends Controller
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

    /**
     * Store a newly created resource in storage.
     */
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
    public function update(UpdateCurrentModelReques $request, int $id)
    {
      return $this->apiResponse(function () use ($request, $id) {
            $row = CurrentModel::findOrFail($id);
            $row->update($request->validated());
            return $row;
        });
    }
    
    public function destroy(int $id,  )
    {
        
     return $this->apiResponse(function () use ($id) {
            CurrentModel::findOrFail($id)->delete();
            return ['id' => $id];
            
        });
     }
}
