<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FishCatch;
use App\Models\CatchLog;
use App\Models\Specie;
use App\Models\Lure;

class FishCatchSeeder extends Seeder
{
    public function run(): void
    {
        $catchLogs = CatchLog::all();
        $speciesIds = Specie::pluck('id')->toArray();
        $lureIds = Lure::pluck('id')->toArray();

        foreach ($catchLogs as $catchLog) {
            // Minden CatchLog-hoz 1-5 random FishCatch
            foreach (range(1, rand(1, 5)) as $i) {
                FishCatch::create([
                    'catch_log_id' => $catchLog->id,
                    'species_id' => $speciesIds[array_rand($speciesIds)],
                    'lure_id' => $lureIds[array_rand($lureIds)],
                    'weight' => rand(1, 10) + rand(0,99)/100,   // pl. 3.57 kg
                    'length' => rand(20, 100) + rand(0,99)/100, // pl. 65.32 cm
                    'catch_time' => now()->subHours(rand(1, 72)), // 1-72 órával ezelőtti idő
                ]);
            }
        }
    }
}
