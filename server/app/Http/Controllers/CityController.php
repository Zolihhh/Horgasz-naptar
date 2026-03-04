<?php

namespace App\Http\Controllers;

use App\Models\City as CurrentModel;

class CityController extends Controller
{
    public function index()
    {
        return $this->apiResponse(function () {
            return CurrentModel::query()
                ->orderBy('name')
                ->get();
        });
    }
}
