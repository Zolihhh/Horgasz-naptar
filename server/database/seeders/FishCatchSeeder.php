<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FishCatch;


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
