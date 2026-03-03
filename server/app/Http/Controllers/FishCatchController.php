<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFishCatchRequest as StoreCurrentModelRequest;
use App\Http\Requests\UpdateFishCatchRequest as UpdateCurrentModelRequest;
use App\Models\CatchLog;
use App\Models\FishCatch as CurrentModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FishCatchController extends Controller
{
    private function myCatchesQuery(int $userId): Builder
    {
        return CurrentModel::query()
            ->whereHas('catchLog', function (Builder $query) use ($userId) {
                $query->where('userid', $userId);
            });
    }

    private function ensureCatchLogBelongsToUser(int $catchLogId, int $userId): void
    {
        $ownsCatchLog = CatchLog::query()
            ->where('id', $catchLogId)
            ->where('userid', $userId)
            ->exists();

        if (!$ownsCatchLog) {
            throw new HttpException(403, 'Ehhez a fogasi naplohoz nincs jogosultsagod.');
        }
    }

    public function index(Request $request)
    {
        return $this->apiResponse(function () use ($request) {
            return $this->myCatchesQuery($request->user()->id)->get();
        });
    }

    public function store(StoreCurrentModelRequest $request)
    {
        return $this->apiResponse(function () use ($request) {
            $validated = $request->validated();
            $userId = $request->user()->id;

            $this->ensureCatchLogBelongsToUser($validated['catchLogId'], $userId);

            return CurrentModel::create($validated);
        });
    }

    public function show(Request $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            return $this->myCatchesQuery($request->user()->id)->findOrFail($id);
        });
    }

    public function update(UpdateCurrentModelRequest $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $userId = $request->user()->id;
            $fishCatch = $this->myCatchesQuery($userId)->findOrFail($id);
            $validated = $request->validated();

            $this->ensureCatchLogBelongsToUser($validated['catchLogId'], $userId);

            $fishCatch->update($validated);

            return $fishCatch;
        });
    }

    public function destroy(Request $request, int $id)
    {
        return $this->apiResponse(function () use ($request, $id) {
            $fishCatch = $this->myCatchesQuery($request->user()->id)->findOrFail($id);
            $fishCatch->delete();

            return ['id' => $id];
        });
    }
}
