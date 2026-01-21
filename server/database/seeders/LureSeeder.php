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
        $delimiter = ',';

        $data = CsvReader::csvToArray($fileName, $delimiter);

        if (Lure::count() === 0) {
            // CSV oszlop: id, csali
            foreach ($data as &$row) {
                $row = ['id' => $row['id'], 'lure' => $row['csali'], 'created_at' => now(), 'updated_at' => now()];
            }

            Lure::insert($data);
        }
    }
}
