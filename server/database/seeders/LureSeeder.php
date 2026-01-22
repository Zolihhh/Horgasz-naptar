<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;
use App\Models\Lure;

class LureSeeder extends Seeder
{
    public function run(): void
    {
        $fileName = 'csv/lures.csv';
        $delimiter = ';';

        $data = CsvReader::csvToArray($fileName, $delimiter);
        Lure::factory()->createMany($data);
    }
}
