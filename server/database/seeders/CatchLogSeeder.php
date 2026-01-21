<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatchLog;
use App\Models\FishCatch;
use App\Models\User;
use App\Models\Location;

class CatchLogSeeder extends Seeder
{
    public function run(): void
    {
        // Feltételezzük, hogy van legalább 1 user és 1 location
        $user = User::first();
        $locations = Location::all();
        $speciesIds = [1, 2, 3]; // feltételezett species ID-k
        $lureIds = [1, 2, 3];    // feltételezett lure ID-k

        // 5 db teszt CatchLog
        foreach (range(1, 5) as $i) {
            $catchLog = CatchLog::create([
                'userid' => $user->id,
                'fishing_lake_id' => $locations->random()->id,
                'comment' => "Teszt catch log $i",
                'fishing_start' => now()->subDays(rand(1,10)),
                'fishing_end' => now(),
            ]);

            // minden CatchLog-hoz 1-3 FishCatch
            foreach (range(1, rand(1,3)) as $j) {
                FishCatch::create([
                    'catch_log_id' => $catchLog->id,
                    'species_id' => $speciesIds[array_rand($speciesIds)],
                    'weight' => rand(1, 10) + rand(0,99)/100, // pl. 3.57
                    'length' => rand(20, 100) + rand(0,99)/100,
                    'lure_id' => $lureIds[array_rand($lureIds)],
                    'catch_time' => now()->subHours(rand(1,10)),
                ]);
            }
        }
    }
}
