<?php

namespace App\Http\Controllers;

use App\Models\CatchLog as CurrentModel;
use App\Http\Requests\StoreCatchLogRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateCatchLogRequest as UpdateCurrentModelRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CatchLogController extends Controller
{
    private function myCatchLogsQuery(int $userId): Builder
    {
        return CurrentModel::query()
            ->where('userid', $userId);
    }

    public function index(Request $request)
    {
        return $this->apiResponse(function () use ($request) {
            return $this->myCatchLogsQuery($request->user()->id)
                ->orderByDesc('fishing_start')
                ->orderByDesc('id')
                ->get();
        });
    }

    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $validated = $request->validated();
            $validated['userid'] = $request->user()->id;

            return CurrentModel::create($validated);
        });
    }

    public function show(Request $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            return $this->myCatchLogsQuery($request->user()->id)->findOrFail($id);
        });
    }

    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $row = $this->myCatchLogsQuery($request->user()->id)->findOrFail($id);
            $validated = $request->validated();
            $validated['userid'] = $request->user()->id;
            $row->update($validated);

            return $row;
        });
    }

    public function destroy(Request $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $this->myCatchLogsQuery($request->user()->id)->findOrFail($id)->delete();

            return ['id' => $id];
        });
    }
}
