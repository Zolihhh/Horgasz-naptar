<?php

namespace App\Http\Controllers;

use App\Models\Specie;
use App\Http\Requests\StoreSpecieRequest;
use App\Http\Requests\UpdateSpecieRequest;
use Illuminate\Http\Request;

class SpecieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Lekérdezi az összes fajt
        $species = Specie::orderBy('id')->get();

        // Ha nincs adat → visszaad egy üzenetet
        if ($species->isEmpty()) {
            return response()->json([
                'message' => 'Nincs adat a species táblában. Futtasd a seeder-t!'
            ], 200);
        }

        // JSON visszaadás
        return response()->json($species);
    

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSpecieRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Specie $specie)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSpecieRequest $request, Specie $specie)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Specie $specie)
    {
        //
    }
}
