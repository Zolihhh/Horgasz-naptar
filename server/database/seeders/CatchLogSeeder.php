<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CatchLog;


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
