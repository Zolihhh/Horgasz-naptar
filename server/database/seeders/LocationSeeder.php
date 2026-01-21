<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Helpers\CsvReader;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $fileName = 'csv/locations.csv';
        $delimiter = ';';

        $data = CsvReader::csvToArray($fileName, $delimiter);
        // cdd($data);

        if (Location::count() === 0) {
            Location::insert($data);
        }
    }
}
