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
    public function run(): void
    {
        for ($i=0; $i < 5; $i++) { 
            # code...
            CatchLog::factory()->create();
        }
    }
}
