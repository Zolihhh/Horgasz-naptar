<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatchLog;
use App\Models\FishCatch;
use App\Models\User;
use App\Models\Location;
use App\Models\Specie;
use App\Models\Lure;

class CatchLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $locations = Location::all();
        $species = Specie::all();
        $lures = Lure::all();

        // ha nincs elég adat, nem seedelünk
        if (
            $users->isEmpty() ||
            $locations->isEmpty() ||
            $species->isEmpty() ||
            $lures->isEmpty()
        ) {
            return;
        }

        // hány catch log legyen
        $numberOfCatchLogs = round($users->count() * 2);

        for ($i = 0; $i < $numberOfCatchLogs; $i++) {

            $user = $users->random();
            $location = $locations->random();

            $catchLog = CatchLog::create([
                'userid' => $user->id,
                'fishing_lake_id' => $location->id,
                'comment' => 'Seeder által generált fogásnapló',
                'fishing_start' => now()->subHours(rand(2, 10)),
                'fishing_end' => now(),
            ]);

            // 1–3 hal egy naplóhoz
            $numberOfFishCatches = rand(1, 3);

            for ($j = 0; $j < $numberOfFishCatches; $j++) {
                FishCatch::create([
                    'catch_log_id' => $catchLog->id,
                    'species_id' => $species->random()->id,
                    'lure_id' => $lures->random()->id,
                    'weight' => rand(50, 1500) / 100, // 0.5 – 15 kg
                    'length' => rand(20, 120),
                    'catch_time' => now()->subMinutes(rand(10, 300)),
                ]);
            }
        }
    }
}
