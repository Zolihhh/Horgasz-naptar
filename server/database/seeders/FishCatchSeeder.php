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
        for ($i = 0; $i < 5; $i++) {
            # code...
            FishCatch::factory()->create();
        }

    }
}
